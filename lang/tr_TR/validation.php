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

		'accepted' => ':attribute alanı kabul edilmeli.',
		'accepted_if' => ':other :value olduğunda :attribute alanı kabul edilmeli.',
		'active_url' => ':attribute alanı geçerli bir URL olmalı.',
		'after' => ':attribute alanı :date tarihinden sonraki bir tarih olmalı.',
		'after_or_equal' => ':attribute alanı :date tarihinden sonra veya eşit bir tarih olmalı.',
		'alpha' => ':attribute alanı sadece harflerden oluşmalı.',
		'alpha_dash' => ':attribute alanı sadece hafr, rakam, tire veya alt çizgi içermeli.',
		'alpha_num' => ':attribute alanı sadece harfler ve rakamlar içermeli.',
		'array' => ':attribute alanı bir dizi olmalı.',
		'ascii' => ':attribute alanı yalnızca tek baytlık alfanümerik karakterler ve semboller içermeli.',
		'before' => ':attribute alanı :date tarihinden önceki bir tarih olmalı.',
		'before_or_equal' => ':attribute alanı :date tarihinden önce veya eşit bir tarih olmalı.',
		'between' => [
			'array' => ':attribute alanı :min ile :max öğe arasında olmalı.',
			'file' => ':attribute alanı :min ile :max kilobayt arasında olmalı.',
			'numeric' => ':attribute alanı :min ile :max arasında olmalı.',
			'string' => ':attribute alanı :min ile :max karakter arasında olmalı.',
		],
		'boolean' => ':attribute alanı true veya false olmalı.',
		'confirmed' => ':attribute alanı doğrulaması eşleşmiyor.',
		'current_password' => 'Parola hatalı.',
		'date' => ':attribute alanı geçerli bir tarih olmalı.',
		'date_equals' => ':attribute alanı :date tarihine eşit olmalı.',
		'date_format' => ':attribute alanı :format formatına uygun olmalı.',
		'decimal' => ':attribute alanı :decimal ondalık basamağa sahip olmalı.',
		'declined' => ':attribute alanı reddedilmeli.',
		'declined_if' => ':other :value olduğunda :attribute alanı reddedilmeli.',
		'different' => ':attribute alanı ile :other birbirinden farklı olmalı.',
		'digits' => ':attribute alanı :digits haneli olmalı.',
		'digits_between' => ':attribute alanı :min ile :max hane arasında olmalı.',
		'dimensions' => ':attribute alanı geçersiz resim boyutlarına sahip.',
		'distinct' => ':attribute alanı yinelenen bir değere sahip.',
		'doesnt_end_with' => ':attribute alanı şunlardan biriyle bitmemelidir: :values.',
		'doesnt_start_with' => ':attribute alanı şunlardan biriyle başlamamalıdır: :values.',
		'email' => ':attribute alanı geçerli bir e-posta adresi olmalı.',
		'ends_with' => ':attribute alanı şunlardan biriyle bitmelidir: :values.',
		'enum' => 'Seçilen :attribute geçersiz.',
		'exists' => 'Seçilen :attribute geçersiz.',
		'file' => ':attribute alanı bir dosya olmalı.',
		'filled' => ':attribute alanının bir değeri olmalı.',
		'gt' => [
			'array' => ':attribute alanı :value öğeden fazla olmalı.',
			'file' => ':attribute alanı :value kilobayttan büyük olmalı.',
			'numeric' => ':attribute alanı :value değerinden büyük olmalı.',
			'string' => ':attribute alanı :value karakterden uzun olmalı.',
		],
		'gte' => [
			'array' => ':attribute alanı :value veya daha fazla öğeye sahip olmalı.',
			'file' => ':attribute alanı :value kilobayta eşit veya daha büyük olmalı.',
			'numeric' => ':attribute alanı :value değerine eşit veya daha büyük olmalı.',
			'string' => ':attribute alanı :value karakter veya daha uzun olmalı.',
		],
		'image' => ':attribute alanı bir resim olmalı.',
		'in' => 'Seçili :attribute geçersiz.',
		'in_array' => ':attribute alanı :other içinde mevcut olmalı.',
		'integer' => ':attribute alanı bir tam sayı olmalı.',
		'ip' => ':attribute alanı geçerli bir IP adresi olmalı.',
		'ipv4' => ':attribute alanı geçerli bir IPv4 adresi olmalı.',
		'ipv6' => ':attribute alanı geçerli bir IPv6 adresi olmalı.',
		'json' => ':attribute alanı geçerli bir JSON dizesi olmalı.',
		'lowercase' => ':attribute alanı küçük harf olmalı.',
		'lt' => [
			'array' => ':attribute alanı :value öğeden az olmalı.',
			'file' => ':attribute alanı :value kilobayttan küçük olmalı.',
			'numeric' => ':attribute alanı :value değerinden küçük olmalı.',
			'string' => ':attribute alanı :value karakterden az olmalı.',
		],
		'lte' => [
			'array' => ':attribute alanı :value öğeden fazla olmamalıdır.',
			'file' => ':attribute alanı :value kilobayta eşit veya daha küçük olmalı.',
			'numeric' => ':attribute alanı :value değerine eşit veya daha küçük olmalı.',
			'string' => ':attribute alanı :value karakter veya daha az olmalı.',
		],
		'mac_address' => ':attribute alanı geçerli bir MAC adresi olmalı.',
		'max' => [
			'array' => ':attribute alanı :max öğeden fazla olmamalıdır.',
			'file' => ':attribute alanı :max kilobayttan büyük olmamalıdır.',
			'numeric' => ':attribute alanı :max değerinden büyük olmamalıdır.',
			'string' => ':attribute alanı :max karakterden fazla olmamalıdır.',
		],
		'max_digits' => ':attribute alanı :max basamaktan fazla olmamalıdır.',
		'mimes' => ':attribute alanı şu dosya türlerinden biri olmalı: :values.',
		'mimetypes' => ':attribute alanı şu dosya türlerinden biri olmalı: :values.',
		'min' => [
			'array' => ':attribute alanı en az :min öğeye sahip olmalı.',
			'file' => ':attribute alanı en az :min kilobayt olmalı.',
			'numeric' => ':attribute alanı en az :min olmalı.',
			'string' => ':attribute alanı en az :min karakter olmalı.',
		],
		'min_digits' => ':attribute alanı en az :min basamaklı olmalı.',
		'missing' => ':attribute alanı eksik olmalı.',
		'missing_if' => ':other değeri :value olduğunda :attribute alanı eksik olmalı.',
		'missing_unless' => ':other değeri :value olmadığında :attribute alanı eksik olmalı.',
		'missing_with' => ':values mevcut olduğunda :attribute alanı eksik olmalı.',
		'missing_with_all' => ':values mevcut olduğunda :attribute alanı eksik olmalı.',
		'multiple_of' => ':attribute alanı :value değerinin katı olmalı.',
		'not_in' => 'Seçili :attribute geçersiz.',
		'not_regex' => ':attribute alanı formatı geçersiz.',
		'numeric' => ':attribute alanı sayı olmalı.',

		'password' => [
			'letters' => ':attribute alanı en az bir harf içermeli.',
			'mixed' => ':attribute alanı en az bir büyük ve bir küçük harf içermeli.',
			'numbers' => ':attribute alanı en az bir rakam içermeli.',
			'symbols' => ':attribute alanı en az bir sembol içermeli.',
			'uncompromised' => 'Girilen :attribute bir veri sızıntısında görünmüş. Lütfen farklı bir :attribute seçin.',
		],
		'present' => ':attribute alanı mevcut olmalı.',
		'prohibited' => ':attribute alanı yasaktır.',
		'prohibited_if' => ':other :value olduğunda :attribute alanı yasaktır.',
		'prohibited_unless' => ':other :values içinde olmadığı sürece :attribute alanı yasaktır.',
		'prohibits' => ':attribute alanı, :other alanının mevcut olmasını yasaklar.',

		'regex' => 'The :attribute field format is invalid.',
		'required' => ':attribute alanı zorunludur.',
		'required_array_keys' => ':attribute alanı şunlar için girişler içermeli: :values.',
		'required_if' => ':other :value olduğunda :attribute alanı zorunludur.',
		'required_if_accepted' => ':other kabul edildiğinde :attribute alanı zorunludur.',
		'required_unless' => ':other :values içinde olmadığı sürece :attribute alanı zorunludur.',
		'required_with' => ':values mevcut olduğunda :attribute alanı zorunludur.',
		'required_with_all' => ':values mevcut olduğunda :attribute alanı zorunludur.',
		'required_without' => ':values mevcut olmadığında :attribute alanı zorunludur.',
		'required_without_all' => ':values değerlerinden hiçbiri mevcut olmadığında :attribute alanı zorunludur.',
		'same' => ':attribute alanı :other ile eşleşmelidir.',
		'size' => [
			'array' => ':attribute alanı :size öğe içermeli.',
			'file' => ':attribute alanı :size kilobayt olmalı.',
			'numeric' => ':attribute alanı :size olmalı.',
			'string' => ':attribute alanı :size karakter olmalı.',
		],

		'starts_with' => ':attribute alanı şunlardan biri ile başlamalıdır: :values.',
		'string' => ':attribute alanı bir metin dizisi olmalı.',
		'timezone' => ':attribute alanı geçerli bir saat dilimi olmalı.',
		'unique' => ':attribute daha önceden kayıt edilmiş.',
		'uploaded' => ':attribute yüklemesi başarısız.',
		'uppercase' => ':attribute alanı büyük harf olmalı.',
		'url' => ':attribute alanı geçerli bir URL olmalı.',
		'ulid' => ':attribute alanı geçerli bir ULID olmalı.',
		'uuid' => ':attribute alanı geçerli bir UUID olmalı.',
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
