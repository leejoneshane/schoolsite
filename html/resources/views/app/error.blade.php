@extends('layouts.main')

@section('content')
<div class="w-full h-full text-2xl font-bold">
	<div class="leading-normal pb-5">
		系統提示
		<a class="text-sm py-2 pl-6 rounded text-blue-300 hover:text-blue-600" href="{{ route('clubs') }}">
			<i class="fa-solid fa-eject"></i>返回上一頁
		</a>
	</div>
	<div class="border-red-500 bg-red-100 dark:bg-red-700 border-b-2 mb-5" role="alert">
		<p>
			{{ $message }}
		</p>
	</div>	
</div>
@endsection
