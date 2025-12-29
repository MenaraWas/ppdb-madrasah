<h1>PPDB</h1>

@if(!$activeYear)
  <p>Belum ada Tahun Ajaran aktif.</p>
@else
  <p>Tahun Ajaran: {{ $activeYear->name }}</p>

  <form method="POST" action="{{ route('ppdb.initiate') }}">
    @csrf

    <label>NISN</label><input name="nisn" /><br/>
    <label>Nama</label><input name="name" /><br/>
    <label>WhatsApp</label><input name="whatsapp" /><br/>

    <label>Gelombang</label>
    <select name="wave_id">
      @foreach($waves as $w)
        <option value="{{ $w->id }}">{{ $w->name }} ({{ $w->status }})</option>
      @endforeach
    </select><br/>

    <label>Jalur</label>
    <select name="track_id">
      @foreach($waves as $w)
        @foreach($w->tracks as $t)
          <option value="{{ $t->id }}">{{ $t->name }} - {{ $w->name }}</option>
        @endforeach
      @endforeach
    </select><br/>

    <button type="submit">Mulai / Register</button>
  </form>
@endif
