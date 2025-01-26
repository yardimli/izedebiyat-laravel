<?php

	return [

		/*
		|--------------------------------------------------------------------------
		| Validation Language Lines
		|--------------------------------------------------------------------------
		|
		| The following language lines contain the default error messages used by
		| the validator class. Some of these rules have multiple versions such
		| as the size rules. Feel free to tweak each of these messages here.
		|
		*/

		'accepted' => ':attribute alanı kabul edilmelidir.',
		'accepted_if' => ':other :value olduğunda :attribute alanı kabul edilmelidir.',
		'active_url' => ':attribute alanı geçerli bir URL olmalıdır.',
		'after' => ':attribute alanı :date tarihinden sonraki bir tarih olmalıdır.',
		'after_or_equal' => ':attribute alanı :date tarihinden sonra veya eşit bir tarih olmalıdır.',
		'alpha' => ':attribute alanı sadece harflerden oluşmalıdır.',
		'alpha_dash' => ':attribute alanı sadece harfler, rakamlar, tireler ve alt çizgiler içermelidir.',
		'alpha_num' => ':attribute alanı sadece harfler ve rakamlar içermelidir.',
		'array' => ':attribute alanı bir dizi olmalıdır.',
		'ascii' => ':attribute alanı yalnızca tek baytlık alfanümerik karakterler ve semboller içermelidir.',
		'before' => ':attribute alanı :date tarihinden önceki bir tarih olmalıdır.',
		'before_or_equal' => ':attribute alanı :date tarihinden önce veya eşit bir tarih olmalıdır.',
		'between' => [
			'array' => ':attribute alanı :min ile :max öğe arasında olmalıdır.',
			'file' => ':attribute alanı :min ile :max kilobayt arasında olmalıdır.',
			'numeric' => ':attribute alanı :min ile :max arasında olmalıdır.',
			'string' => ':attribute alanı :min ile :max karakter arasında olmalıdır.',
		],
		'boolean' => ':attribute alanı true veya false olmalıdır.',
		'confirmed' => ':attribute alanı doğrulaması eşleşmiyor.',
		'current_password' => 'Parola hatalı.',
		'date' => ':attribute alanı geçerli bir tarih olmalıdır.',
		'date_equals' => ':attribute alanı :date tarihine eşit olmalıdır.',
		'date_format' => ':attribute alanı :format formatına uygun olmalıdır.',
		'decimal' => ':attribute alanı :decimal ondalık basamağa sahip olmalıdır.',
		'declined' => ':attribute alanı reddedilmelidir.',
		'declined_if' => ':other :value olduğunda :attribute alanı reddedilmelidir.',
		'different' => ':attribute alanı ile :other birbirinden farklı olmalıdır.',
		'digits' => ':attribute alanı :digits haneli olmalıdır.',
		'digits_between' => ':attribute alanı :min ile :max hane arasında olmalıdır.',
		'dimensions' => ':attribute alanı geçersiz resim boyutlarına sahip.',
		'distinct' => ':attribute alanı yinelenen bir değere sahip.',
		'doesnt_end_with' => ':attribute alanı şunlardan biriyle bitmemelidir: :values.',
		'doesnt_start_with' => ':attribute alanı şunlardan biriyle başlamamalıdır: :values.',
		'email' => ':attribute alanı geçerli bir e-posta adresi olmalıdır.',
		'ends_with' => ':attribute alanı şunlardan biriyle bitmelidir: :values.',
		'enum' => 'Seçilen :attribute geçersiz.',
		'exists' => 'Seçilen :attribute geçersiz.',
		'file' => ':attribute alanı bir dosya olmalıdır.',
		'filled' => ':attribute alanının bir değeri olmalıdır.',
		'gt' => [
			'array' => ':attribute alanı :value öğeden fazla olmalıdır.',
			'file' => ':attribute alanı :value kilobayttan büyük olmalıdır.',
			'numeric' => ':attribute alanı :value değerinden büyük olmalıdır.',
			'string' => ':attribute alanı :value karakterden uzun olmalıdır.',
		],
		'gte' => [
			'array' => ':attribute alanı :value veya daha fazla öğeye sahip olmalıdır.',
			'file' => ':attribute alanı :value kilobayta eşit veya daha büyük olmalıdır.',
			'numeric' => ':attribute alanı :value değerine eşit veya daha büyük olmalıdır.',
			'string' => ':attribute alanı :value karakter veya daha uzun olmalıdır.',
		],
		'image' => ':attribute alanı bir resim olmalıdır.',
		'in' => 'Seçili :attribute geçersiz.',
		'in_array' => ':attribute alanı :other içinde mevcut olmalıdır.',
		'integer' => ':attribute alanı bir tam sayı olmalıdır.',
		'ip' => ':attribute alanı geçerli bir IP adresi olmalıdır.',
		'ipv4' => ':attribute alanı geçerli bir IPv4 adresi olmalıdır.',
		'ipv6' => ':attribute alanı geçerli bir IPv6 adresi olmalıdır.',
		'json' => ':attribute alanı geçerli bir JSON dizesi olmalıdır.',
		'lowercase' => ':attribute alanı küçük harf olmalıdır.',
		'lt' => [
			'array' => ':attribute alanı :value öğeden az olmalıdır.',
			'file' => ':attribute alanı :value kilobayttan küçük olmalıdır.',
			'numeric' => ':attribute alanı :value değerinden küçük olmalıdır.',
			'string' => ':attribute alanı :value karakterden az olmalıdır.',
		],
		'lte' => [
			'array' => ':attribute alanı :value öğeden fazla olmamalıdır.',
			'file' => ':attribute alanı :value kilobayta eşit veya daha küçük olmalıdır.',
			'numeric' => ':attribute alanı :value değerine eşit veya daha küçük olmalıdır.',
			'string' => ':attribute alanı :value karakter veya daha az olmalıdır.',
		],
		'mac_address' => ':attribute alanı geçerli bir MAC adresi olmalıdır.',
		'max' => [
			'array' => ':attribute alanı :max öğeden fazla olmamalıdır.',
			'file' => ':attribute alanı :max kilobayttan büyük olmamalıdır.',
			'numeric' => ':attribute alanı :max değerinden büyük olmamalıdır.',
			'string' => ':attribute alanı :max karakterden fazla olmamalıdır.',
		],
		'max_digits' => ':attribute alanı :max basamaktan fazla olmamalıdır.',
		'mimes' => ':attribute alanı şu dosya türlerinden biri olmalıdır: :values.',
		'mimetypes' => ':attribute alanı şu dosya türlerinden biri olmalıdır: :values.',
		'min' => [
			'array' => ':attribute alanı en az :min öğeye sahip olmalıdır.',
			'file' => ':attribute alanı en az :min kilobayt olmalıdır.',
			'numeric' => ':attribute alanı en az :min olmalıdır.',
			'string' => ':attribute alanı en az :min karakter olmalıdır.',
		],
		'min_digits' => ':attribute alanı en az :min basamaklı olmalıdır.',
		'missing' => ':attribute alanı eksik olmalıdır.',
		'missing_if' => ':other değeri :value olduğunda :attribute alanı eksik olmalıdır.',
		'missing_unless' => ':other değeri :value olmadığında :attribute alanı eksik olmalıdır.',
		'missing_with' => ':values mevcut olduğunda :attribute alanı eksik olmalıdır.',
		'missing_with_all' => ':values mevcut olduğunda :attribute alanı eksik olmalıdır.',
		'multiple_of' => ':attribute alanı :value değerinin katı olmalıdır.',
		'not_in' => 'Seçili :attribute geçersiz.',
		'not_regex' => ':attribute alanı formatı geçersiz.',
		'numeric' => ':attribute alanı sayı olmalıdır.',

		'password' => [
			'letters' => ':attribute alanı en az bir harf içermelidir.',
			'mixed' => ':attribute alanı en az bir büyük ve bir küçük harf içermelidir.',
			'numbers' => ':attribute alanı en az bir rakam içermelidir.',
			'symbols' => ':attribute alanı en az bir sembol içermelidir.',
			'uncompromised' => 'Girilen :attribute bir veri sızıntısında görünmüş. Lütfen farklı bir :attribute seçin.',
		],
		'present' => ':attribute alanı mevcut olmalıdır.',
		'prohibited' => ':attribute alanı yasaktır.',
		'prohibited_if' => ':other :value olduğunda :attribute alanı yasaktır.',
		'prohibited_unless' => ':other :values içinde olmadığı sürece :attribute alanı yasaktır.',
		'prohibits' => ':attribute alanı, :other alanının mevcut olmasını yasaklar.',

		'regex' => 'The :attribute field format is invalid.',
		'required' => ':attribute alanı zorunludur.',
		'required_array_keys' => ':attribute alanı şunlar için girişler içermelidir: :values.',
		'required_if' => ':other :value olduğunda :attribute alanı zorunludur.',
		'required_if_accepted' => ':other kabul edildiğinde :attribute alanı zorunludur.',
		'required_unless' => ':other :values içinde olmadığı sürece :attribute alanı zorunludur.',
		'required_with' => ':values mevcut olduğunda :attribute alanı zorunludur.',
		'required_with_all' => ':values mevcut olduğunda :attribute alanı zorunludur.',
		'required_without' => ':values mevcut olmadığında :attribute alanı zorunludur.',
		'required_without_all' => ':values değerlerinden hiçbiri mevcut olmadığında :attribute alanı zorunludur.',
		'same' => ':attribute alanı :other ile eşleşmelidir.',
		'size' => [
			'array' => ':attribute alanı :size öğe içermelidir.',
			'file' => ':attribute alanı :size kilobayt olmalıdır.',
			'numeric' => ':attribute alanı :size olmalıdır.',
			'string' => ':attribute alanı :size karakter olmalıdır.',
		],

		'starts_with' => ':attribute alanı şunlardan biri ile başlamalıdır: :values.',
		'string' => ':attribute alanı bir metin dizisi olmalıdır.',
		'timezone' => ':attribute alanı geçerli bir saat dilimi olmalıdır.',
		'unique' => ':attribute daha önceden kayıt edilmiş.',
		'uploaded' => ':attribute yüklemesi başarısız.',
		'uppercase' => ':attribute alanı büyük harf olmalıdır.',
		'url' => ':attribute alanı geçerli bir URL olmalıdır.',
		'ulid' => ':attribute alanı geçerli bir ULID olmalıdır.',
		'uuid' => ':attribute alanı geçerli bir UUID olmalıdır.',
		/*
		|--------------------------------------------------------------------------
		| Custom Validation Language Lines
		|--------------------------------------------------------------------------
		|
		| Here you may specify custom validation messages for attributes using the
		| convention "attribute.rule" to name the lines. This makes it quick to
		| specify a specific custom language line for a given attribute rule.
		|
		*/

		'custom' => [
			'attribute-name' => [
				'rule-name' => 'custom-message',
			],
		],

		/*
		|--------------------------------------------------------------------------
		| Custom Validation Attributes
		|--------------------------------------------------------------------------
		|
		| The following language lines are used to swap our attribute placeholder
		| with something more reader friendly such as "E-Mail Address" instead
		| of "email". This simply helps us make our message more expressive.
		|
		*/

		'attributes' => [],

	];
