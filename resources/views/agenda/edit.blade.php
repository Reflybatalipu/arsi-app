@extends('layouts.app')

@section('title', 'Ubah Agenda')

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="mb-4 text-xl font-semibold">Ubah Agenda</h1>

        <div class="rounded-lg bg-white p-6 shadow">
            <form method="POST" action="{{ route('agenda.update', $agenda) }}" class="space-y-4">
                @csrf
                @method('PUT')
                @include('agenda._form')

                <div class="flex gap-3">
                    <button type="submit"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        Perbarui
                    </button>
                    <a href="{{ route('agenda.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
