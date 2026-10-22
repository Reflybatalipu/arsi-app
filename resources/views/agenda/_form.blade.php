{{--
    Form bersama untuk tambah / ubah agenda.
    Variabel: $agenda (opsional, untuk mode ubah)
--}}
<div class="space-y-4">
    <div>
        <label for="judul" class="label"><x-fa name="font" class="text-gray-400" />Judul Agenda</label>
        <input type="text" name="judul" id="judul" value="{{ old('judul', $agenda->judul ?? '') }}" required
               class="input">
    </div>

    <div>
        <label for="jenis" class="label"><x-fa name="tag" class="text-gray-400" />Jenis</label>
        <select name="jenis" id="jenis" required class="input cursor-pointer">
            @foreach (['organisasi' => 'Kegiatan Organisasi', 'rencana' => 'Rencana', 'lainnya' => 'Lainnya'] as $value => $label)
                <option value="{{ $value }}" @selected(old('jenis', $agenda->jenis ?? 'lainnya') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="tanggal" class="label"><x-fa name="calendar-days" class="text-gray-400" />Tanggal</label>
            <input type="date" name="tanggal" id="tanggal"
                   value="{{ old('tanggal', isset($agenda) && $agenda->tanggal ? $agenda->tanggal->format('Y-m-d') : '') }}" required
                   class="input">
        </div>
        <div>
            <label for="waktu" class="label"><x-fa name="clock" class="text-gray-400" />Waktu (opsional)</label>
            <input type="time" name="waktu" id="waktu" value="{{ old('waktu', $agenda->waktu ?? '') }}"
                   class="input">
        </div>
    </div>

    <div>
        <label for="deskripsi" class="label"><x-fa name="align-left" class="text-gray-400" />Deskripsi</label>
        <textarea name="deskripsi" id="deskripsi" rows="4" class="input">{{ old('deskripsi', $agenda->deskripsi ?? '') }}</textarea>
    </div>
</div>
