@extends('layout')

@section('content')

<div class="max-w-6xl mx-auto py-12">
  <h2 class="text-2xl font-bold mb-6">All Companies</h2>

  <div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <table class="min-w-full">
      <thead>
        <tr>
          <th class="px-6 py-3 border-b text-left text-sm font-medium text-gray-500">Company Name</th>
          <th class="px-6 py-3 border-b text-left text-sm font-medium text-gray-500">Created At</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($companies as $company)
          <tr class="bg-white border-b">
            <td class="px-6 py-4 text-sm text-gray-900">{{ $company->name }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ $company->created_at->format('Y-m-d') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection
