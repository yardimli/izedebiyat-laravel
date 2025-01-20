<?php

	namespace App\Http\Controllers;

	use App\Helpers\MyHelper;
	use App\Models\Kategori;
	use App\Models\Yazi;
	use Carbon\Carbon;
	use GuzzleHttp\Client;
	use Illuminate\Http\Request;
	use App\Models\User;
	use Illuminate\Pagination\LengthAwarePaginator;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\File;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Storage;
	use Illuminate\Support\Str;
	use Illuminate\Support\Facades\Validator;


	class FrontendController extends Controller
	{

		public function index()
		{
			$categories = Kategori::where('ust_kategori_id', 0)
				->orderBy('kategori_ad')
				->get();

			foreach ($categories as $category) {
				$category->yazilar = Yazi::where('ust_kategori_id', $category->id)
					->where('onay', 1)
					->where('silindi', 0)
					->where('bad_critical', '<', 3)
					->where('religious_moderation_value', '<', 3)
					->where('respect_moderation_value', '>=', 3)
					->where('moderation_flagged', 0)
					->orderBy('formul_ekim', 'DESC')
					->limit(100)
					->get();

				$category->yeni_yazilar = Yazi::where('ust_kategori_id', $category->id)
					->where('onay', 1)
					->where('silindi', 0)
					->where('bad_critical', '<', 3)
					->where('religious_moderation_value', '<', 3)
					->where('respect_moderation_value', '>=', 3)
					->where('moderation_flagged', 0)
					->orderBy('id', 'DESC')
					->limit(100)
					->get();
			}

			//dd($categories);

			return view('frontend.index', compact('categories'));
		}

	}
