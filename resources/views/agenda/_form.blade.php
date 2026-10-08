{{--
    Form bersama untuk tambah / ubah agenda.
    Variabel: $agenda (opsional, untuk mode ubah)
--}}
<div class="space-y-4">
    <div>
        <label for="judul" class="mb-1 block text-sm">Judul Agenda</label>
        <input type="text" name="judul" id="judul" value="{{ old('judul', $agenda->judul ?? '') }}" required
               class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
    </div>

    <div>
        <label for="jenis" class="mb-1 block text-sm">Jenis</label>
        <select name="jenis" id="jenis" required
                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
            @foreach (['organisasi' => 'Kegiatan Organisasi', 'rencana' => 'Rencana', 'lainnya' => 'Lainnya'] as $value => $label)
                <option value="{{ $value }}" @selected(old('jenis', $agenda->jenis ?? 'lainnya') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="tanggal" class="mb-1 block text-sm">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', isset($agenda) && $agenda->tanggal ? $agenda->tanggal->format('Y-m-d') : '') }}" required
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
        </div>
        <div>
            <label for="waktu" class="mb-1 block text-sm">Waktu (opsional)</label>
            <input type="time" name="waktu" id="waktu" value="{{ old('waktu', $agenda->waktu ?? '') }}"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
        </div>
    </div>

    <div>
        <label for="deskripsi" class="mb-1 block text-sm">Deskripsi</label>
        <textarea name="deskripsi" id="deskripsi" rows="4"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">{{ old('deskripsi', $agenda->deskripsi ?? '') }}</textarea>
    </div>
</div>
