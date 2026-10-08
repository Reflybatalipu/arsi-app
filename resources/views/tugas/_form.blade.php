{{--
    Form bersama untuk tambah / ubah tugas.
    Variabel: $tugas (opsional, untuk mode ubah)
--}}
<div class="space-y-4">
    <div>
        <label for="judul" class="mb-1 block text-sm">Judul Tugas</label>
        <input type="text" name="judul" id="judul" value="{{ old('judul', $tugas->judul ?? '') }}" required
               class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="mata_kuliah" class="mb-1 block text-sm">Mata Kuliah</label>
            <input type="text" name="mata_kuliah" id="mata_kuliah" value="{{ old('mata_kuliah', $tugas->mata_kuliah ?? '') }}"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
        </div>
        <div>
            <label for="dosen" class="mb-1 block text-sm">Dosen Pemberi</label>
            <input type="text" name="dosen" id="dosen" value="{{ old('dosen', $tugas->dosen ?? '') }}"
                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
        </div>
    </div>

    <div>
        <label for="deskripsi" class="mb-1 block text-sm">Deskripsi</label>
        <textarea name="deskripsi" id="deskripsi" rows="4"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">{{ old('deskripsi', $tugas->deskripsi ?? '') }}</textarea>
    </div>

    <div>
        <label for="tenggat" class="mb-1 block text-sm">Tenggat</label>
        <input type="date" name="tenggat" id="tenggat" value="{{ old('tenggat', isset($tugas) && $tugas->tenggat ? $tugas->tenggat->format('Y-m-d') : '') }}"
               class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
    </div>
</div>
