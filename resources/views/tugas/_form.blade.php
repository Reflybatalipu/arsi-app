{{--
    Form bersama untuk tambah / ubah tugas.
    Variabel: $tugas (opsional, untuk mode ubah)
--}}
<div class="space-y-4">
    <div>
        <label for="judul" class="label"><x-fa name="font" class="text-ink-4" />Judul Tugas</label>
        <input type="text" name="judul" id="judul" value="{{ old('judul', $tugas->judul ?? '') }}" required
               class="input">
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="mata_kuliah" class="label"><x-fa name="graduation-cap" class="text-ink-4" />Mata Kuliah</label>
            <input type="text" name="mata_kuliah" id="mata_kuliah" value="{{ old('mata_kuliah', $tugas->mata_kuliah ?? '') }}"
                   class="input">
        </div>
        <div>
            <label for="dosen" class="label"><x-fa name="user-tie" class="text-ink-4" />Dosen Pemberi</label>
            <input type="text" name="dosen" id="dosen" value="{{ old('dosen', $tugas->dosen ?? '') }}"
                   class="input">
        </div>
    </div>

    <div>
        <label for="deskripsi" class="label"><x-fa name="align-left" class="text-ink-4" />Deskripsi</label>
        <textarea name="deskripsi" id="deskripsi" rows="4" class="input">{{ old('deskripsi', $tugas->deskripsi ?? '') }}</textarea>
    </div>

    <div>
        <label for="tenggat" class="label"><x-fa name="calendar-days" class="text-ink-4" />Tenggat</label>
        <input type="date" name="tenggat" id="tenggat"
               value="{{ old('tenggat', isset($tugas) && $tugas->tenggat ? $tugas->tenggat->format('Y-m-d') : '') }}"
               class="input">
    </div>
</div>
