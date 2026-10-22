@extends('layouts.app')

@section('title', $agenda->judul)

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h1 class="flex items-center gap-2 text-xl font-semibold">
                <x-fa name="calendar-days" class="text-indigo-500" />{{ $agenda->judul }}
            </h1>

            <div class="flex gap-2">
                <a href="{{ route('agenda.edit', $agenda) }}" class="btn btn-secondary btn-sm">
                    <x-fa name="pen" /> Ubah
                </a>

                <form method="POST" action="{{ route('agenda.destroy', $agenda) }}"
                      onsubmit="return confirm('Hapus agenda ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <x-fa name="trash" /> Hapus
                    </button>
                </form>
            </div>
        </div>

        <div class="card p-6">
            <dl class="space-y-3 text-sm">
                <div class="flex gap-3">
                    <dt class="dt-label"><x-fa name="tag" class="text-ink-4" />Jenis</dt>
                    <dd>
                        <span class="inline-flex items-center gap-1 rounded-md bg-accent-soft px-2 py-0.5 text-xs text-accent">
                            <x-fa name="calendar-day" />{{ $agenda->jenis }}
                        </span>
                    </dd>
                </div>
                <div class="flex gap-3">
                    <dt class="dt-label"><x-fa name="calendar-days" class="text-ink-4" />Tanggal</dt>
                    <dd>{{ $agenda->tanggal->isoFormat('D MMM Y') }}</dd>
                </div>
                @if ($agenda->waktu)
                    <div class="flex gap-3">
                        <dt class="dt-label"><x-fa name="clock" class="text-ink-4" />Waktu</dt>
                        <dd>{{ $agenda->waktu }}</dd>
                    </div>
                @endif
                @if ($agenda->deskripsi)
                    <div class="flex gap-3">
                        <dt class="dt-label"><x-fa name="align-left" class="text-ink-4" />Deskripsi</dt>
                        <dd class="whitespace-pre-line">{!! nl2br(e($agenda->deskripsi)) !!}</dd>
                    </div>
                @endif
            </dl>

            <div class="mt-6 flex items-center gap-2 border-t border-line pt-4 text-xs text-ink-3">
                <x-fa name="circle-check" class="text-emerald-500 dark:text-emerald-400" />
                Agenda otomatis ditandai selesai setelah waktu pelaksanaan terlampaui.
            </div>
        </div>
    </div>
@endsection
