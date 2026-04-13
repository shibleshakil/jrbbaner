@extends('layouts.app')
@section('title', 'Create Jiwer Rawda Banner')

@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <a href="{{ route('availability_banners.index') }}" class="btn btn-primary">Back to list</a>
        </div>
    </div>
    <div class="content-body">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">New Availability banner</h4>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <form method="POST" action="{{ route('availability_banners.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="from_date">From date<span class="text-danger">*</span></label>
                                    <input type="date" name="from_date" id="from_date" class="form-control" value="{{ old('from_date') }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="to_date">To date<span class="text-danger">*</span></label>
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="{{ old('to_date') }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="hotel_name">Hotel name <span class="text-danger">*</span></label>
                                    <input type="text" name="hotel_name" id="hotel_name" class="form-control" value="{{ old('hotel_name') }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="room_rate">Room rate <span class="text-danger">*</span></label>
                                    <input type="number" name="room_rate" id="room_rate" class="form-control" value="{{ old('room_rate') }}" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="fb">F.B <span class="text-danger">*</span></label>
                                    <input type="number" name="fb" id="fb" class="form-control" value="{{ old('fb') }}" required>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Image 1<small>(240X160)</small></label>
                                    <input type="file" name="image_1" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Image 2<small>(240X160)</small></label>
                                    <input type="file" name="image_2" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Image 3<small>(240X160)</small></label>
                                    <input type="file" name="image_3" class="form-control" accept="image/*">
                                </div>
                                <div class="col-12">
                                    <h5 class="">Footer contacts</h5>
                                    <p class="text-muted small">First two contacts are required. A third contact is optional.</p>
                                </div>
                                @foreach ([0 => 'Contact 1', 1 => 'Contact 2', 2 => 'Contact 3 (optional)'] as $idx => $label)
                                <div class="col-md-12 form-group border rounded py-1 mb-2">
                                    <label class="font-weight-bold">{{ $label }}</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="contact_{{ $idx }}_number" class="small">Phone / WhatsApp @if ($idx < 2)<span class="text-danger">*</span>@endif</label>
                                            <input type="text" name="contacts[{{ $idx }}][number]" id="contact_{{ $idx }}_number" class="form-control"
                                                value="{{ old('contacts.'.$idx.'.number', $defaultContacts[$idx]['number'] ?? '') }}"
                                                @if ($idx < 2) required @endif>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="contact_{{ $idx }}_name" class="small">Name @if ($idx < 2)<span class="text-danger">*</span>@endif</label>
                                            <input type="text" name="contacts[{{ $idx }}][name]" id="contact_{{ $idx }}_name" class="form-control"
                                                value="{{ old('contacts.'.$idx.'.name', $defaultContacts[$idx]['name'] ?? '') }}"
                                                @if ($idx < 2) required @endif>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="contact_{{ $idx }}_location" class="small">Location @if ($idx < 2)<span class="text-danger">*</span>@endif</label>
                                            <input type="text" name="contacts[{{ $idx }}][location]" id="contact_{{ $idx }}_location" class="form-control"
                                                value="{{ old('contacts.'.$idx.'.location', $defaultContacts[$idx]['location'] ?? '') }}"
                                                @if ($idx < 2) required @endif>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-success">Save &amp; generate PNG</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
