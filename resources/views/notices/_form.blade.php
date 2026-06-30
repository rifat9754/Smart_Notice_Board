@csrf
<div class="form-group">
    <label>Title</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $notice->title ?? '') }}" required>
</div>
<div class="form-group">
    <label>Body</label>
    <textarea name="body" class="form-control" rows="4" required>{{ old('body', $notice->body ?? '') }}</textarea>
</div>
<div class="form-group">
    <label>Type</label>
    <select name="type" class="form-control">
        @foreach(['text','image','pdf'] as $t)
            <option value="{{ $t }}" @selected(old('type', $notice->type ?? 'text') == $t)>{{ ucfirst($t) }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label>Attachment (image/PDF, optional)</label>
    <input type="file" name="attachment" class="form-control-file">
    @if(isset($notice) && $notice->file_path)
        <small class="d-block mt-1">Current file: {{ $notice->file_path }}</small>
    @endif
</div>
<div class="form-group">
    <label>Priority</label>
    <select name="priority" class="form-control">
        @foreach(['high','medium','low'] as $p)
            <option value="{{ $p }}" @selected(old('priority', $notice->priority ?? 'medium') == $p)>{{ ucfirst($p) }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label>Status</label>
    <select name="status" class="form-control">
        @foreach(['draft','published','expired'] as $s)
            <option value="{{ $s }}" @selected(old('status', $notice->status ?? 'draft') == $s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label>Target Board</label>
    <select name="board_id" class="form-control">
        <option value="">All boards</option>
        @foreach($boards as $board)
            <option value="{{ $board->id }}" @selected(old('board_id', $notice->board_id ?? '') == $board->id)>{{ $board->name }}</option>
        @endforeach
    </select>
</div>
<div class="row">
    <div class="form-group col-md-3">
        <label>Show from</label>
        <input type="date" name="show_from" class="form-control" value="{{ old('show_from', $notice->show_from ?? '') }}">
    </div>
    <div class="form-group col-md-3">
        <label>Show to</label>
        <input type="date" name="show_to" class="form-control" value="{{ old('show_to', $notice->show_to ?? '') }}">
    </div>
    <div class="form-group col-md-3">
        <label>Time start</label>
        <input type="time" name="time_start" class="form-control" value="{{ old('time_start', $notice->time_start ?? '') }}">
    </div>
    <div class="form-group col-md-3">
        <label>Time end</label>
        <input type="time" name="time_end" class="form-control" value="{{ old('time_end', $notice->time_end ?? '') }}">
    </div>
</div>
<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" name="is_emergency" value="1" class="custom-control-input" id="is_emergency"
            @checked(old('is_emergency', $notice->is_emergency ?? false))>
        <label class="custom-control-label" for="is_emergency">Emergency notice (instantly takes over all boards)</label>
    </div>
</div>