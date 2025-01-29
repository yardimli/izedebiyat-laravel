<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Str;

	return new class extends Migration {
		public function up()
		{
			$users = DB::table('yazar')->get();
			$emailCounts = []; // Track email occurrences

			foreach ($users as $user) {
				// Check if author has any posts in articles table
				$hasArticles = DB::table('articles')
						->where('user_id', $user->id)
						->count() > 0;

				// Skip if no articles
				if (!$hasArticles) {
					continue;
				}

				// Handle duplicate emails
				$originalEmail = $user->eposta;
				$email = $originalEmail;

				if (!isset($emailCounts[$originalEmail])) {
					$emailCounts[$originalEmail] = 0;
				}

				// Check if email already exists in users table or has been used
				while (DB::table('users')->where('email', $email)->exists()) {
					$emailCounts[$originalEmail]++;
					$parts = explode('@', $originalEmail);
					$email = $parts[0] . '-' . $emailCounts[$originalEmail] . '@' . $parts[1];
				}

				$aboutMe = '';

				if ($user->page_title) {
					$aboutMe .= "<h5>Tanıtım</h5>\n<p>" . $user->page_title . "</p>\n\n";
				}

				if ($user->yazar_gecmis) {
					$aboutMe .= "<h5>Geçmiş</h5>\n<p>" . $user->yazar_gecmis . "</p>\n\n";
				}

				if ($user->yazar_konum) {
					$aboutMe .= "<h5>Konum</h5>\n<p>" . $user->yazar_konum . "</p>\n\n";
				}

				if ($user->yazar_ozellik) {
					$aboutMe .= "<h5>Özellikler</h5>\n<p>" . $user->yazar_ozellik . "</p>\n\n";
				}

				if ($user->yazar_etkiler) {
					$aboutMe .= "<h5>Etkiler</h5>\n<p>" . $user->yazar_etkiler . "</p>\n\n";
				}

				if ($user->yazar_benzerler) {
					$aboutMe .= "<h5>Benzer Yazarlar</h5>\n<p>" . $user->yazar_benzerler . "</p>\n\n";
				}

				// Add links if they exist
				$links = [];
				for ($i = 1; $i <= 5; $i++) {
					$linkField = "link{$i}";
					$descField = "link{$i}_aciklama";

					if ($user->$linkField && $linkField !== 'http://') {
						if (empty($descField)) {
							$descField = $linkField;
							$descField = str_replace('http://', '', $descField);
							$descField = str_replace('https://', '', $descField);
						}

						$links[] = "<a href='" . $user->$linkField . "'>" .
							($user->$descField ?: $user->$linkField) . "</a>";
					}
				}

				if (!empty($links)) {
					$aboutMe .= "<h5>Bağlantılar</h5>\n<p>" . implode("<br>\n", $links) . "</p>";
				}

				// Validate katilma_tarih
				// Date validation
				$defaultDate = '2022-02-02 14:02:02';
				$joinDate = $user->katilma_tarih;

				// Check if it's a valid date
				if (!$joinDate || !strtotime($joinDate)) {
					$joinDate = $defaultDate;
				} else {
					// Convert to DateTime for comparison
					$dateTime = new DateTime($joinDate);
					$minDate = new DateTime('1999-01-01');
					$maxDate = new DateTime('2026-12-31');

					if ($dateTime < $minDate || $dateTime > $maxDate) {
						$joinDate = $defaultDate;
					}
				}


				// Only insert if author has articles
				if ($hasArticles) {
					DB::table('users')->insert([
						'id' => $user->id,
						'name' => $user->name,
						'slug' => $user->slug ?: Str::slug($user->name),
						'email' =>  $email,
						'password' => $user->sifre ? Hash::make($user->sifre) : Hash::make(Str::random(12)),
						'username' => $user->nick ?? Str::slug($user->name),
						'avatar' => $user->yazar_portre,
						'picture' => $user->yazar_resim,
						'page_title' => $author->page_title,
						'personal_url' => $author->site_adres,
						'about_me' => $aboutMe,
						'member_status' => $author->approved ? 1 : 0,
						'member_type' => 2, // Assuming 2 is for users
						'last_ip' => $author->ip_log,
						'created_at' => $joinDate,
						'updated_at' => now(),
					]);
					echo "Inserted author: " . $author->name . "\n";
				}
			}

			// Reset the auto-increment to continue after the last transferred ID
			$maxId = DB::table('users')->max('id');
			DB::statement("ALTER TABLE users AUTO_INCREMENT = " . ($maxId + 1));



			$articles = DB::table('articles')
				->whereNotIn('user_id', function($query) {
					$query->select('id')
						->from('users');
				})
				->get();

			foreach($articles as $article) {
				echo $article->title . "\n"; // Assuming 'title' is the column name
			}

//			// After all the users are transferred, clean up articles table
//			DB::table('articles')
//				->whereNotIn('user_id', function($query) {
//					$query->select('id')
//						->from('users');
//				})
//				->delete();

		}

		public function down()
		{
			// Optional: Add cleanup logic if needed
			// DB::table('users')->whereNotNull('original_author_id')->delete();
		}
	};
