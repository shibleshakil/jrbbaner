@extends('layouts.app')
@section('title', 'Promotion Banner')

@section('content')
<div class="content-wrapper">
    <div class="content-header row"></div>
    <div class="content-body">
        <section id="promotion-form">
            <div class="row">
                <div class="col-12">
                    @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Create Promotion Banner (PNG)</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <form method="POST" action="{{ route('promotions.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label>Hero Banner Image</label>
                                            <input type="file" name="hero_banner" class="form-control" accept="image/*" required>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label>Brand Logo (logo.png)</label>
                                            <input type="file" name="logo" class="form-control" accept="image/*" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <label>Room Image 1</label>
                                            <input type="file" name="room_image_1" class="form-control" accept="image/*" required>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label>Room Image 2</label>
                                            <input type="file" name="room_image_2" class="form-control" accept="image/*" required>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label>Room Image 3</label>
                                            <input type="file" name="room_image_3" class="form-control" accept="image/*" required>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label>Room Image 4</label>
                                            <input type="file" name="room_image_4" class="form-control" accept="image/*" required>
                                        </div>
                                    </div>

                                    <hr>
                                    <h5>Offer Details (Maximum 5 rows)</h5>
                                    <div id="offerRows"></div>

                                    <div class="mt-1">
                                        <button class="btn btn-outline-primary" type="button" id="addRowBtn">Add Row</button>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">Generate Banner</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Generated Banners</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body table-responsive">
                                <table class="table table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Period</th>
                                            <th colspan="3">Room Type</th>
                                            <th rowspan="2" style="transform: rotate(-45deg);">Meals</th>
                                        </tr>
                                        <tr>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Double</th>
                                            <th>Triple</th>
                                            <th>Quad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($promotions as $promotion)
                                        @forelse ($promotion->offerDetails as $offerDetail)
                                        <tr>
                                            <td>{{ $offerDetail->from_date }}</td>
                                            <td>{{ $offerDetail->to_date }}</td>
                                            <td>{{ $offerDetail->double_rate }}</td>
                                            <td>{{ $offerDetail->triple_rate }}</td>
                                            <td>{{ $offerDetail->quad_rate }}</td>
                                            <td>{{ $offerDetail->meals }}</td>
                                        </tr>

                                        @empty

                                        @endforelse
                                        <!-- <tr>
                                            <td>{{ $promotion->id }}</td>
                                            <td>{{ $promotion->offerDetails->count() }}</td>
                                            <td>
                                                @if ($promotion->generated_banner_path)
                                                <img src="{{ route('promotions.preview', $promotion) }}" alt="banner" style="height: 140px;">
                                                @else
                                                Not generated
                                                @endif
                                            </td>
                                            <td>
                                                @if ($promotion->generated_banner_path)
                                                <a class="btn btn-sm btn-success" href="{{ route('promotions.download', $promotion) }}">Download PNG</a>
                                                @endif
                                            </td>
                                        </tr> -->
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No banners created yet.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@section('script')
<script>
    (function() {
        const maxRows = 5;
        const container = document.getElementById('offerRows');
        const addBtn = document.getElementById('addRowBtn');
        let rowCount = 0;

        function rowTemplate(index) {
            return `
                <div class="border p-2 mb-2 offer-row">
                    <div class="row">
                        <div class="col-md-2 mb-1">
                            <label>From</label>
                            <input type="date" name="offers[${index}][from_date]" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-1">
                            <label>To</label>
                            <input type="date" name="offers[${index}][to_date]" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-1">
                            <label>Double</label>
                            <input type="number" min="1" name="offers[${index}][double_rate]" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-1">
                            <label>Triple</label>
                            <input type="number" min="1" name="offers[${index}][triple_rate]" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-1">
                            <label>Quad</label>
                            <input type="number" min="1" name="offers[${index}][quad_rate]" class="form-control" required>
                        </div>
                        <div class="col-md-1 mb-1">
                            <label>Meals</label>
                            <input type="text" maxlength="20" name="offers[${index}][meals]" class="form-control" required>
                        </div>
                        <div class="col-md-1 d-flex align-items-end mb-1">
                            <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                        </div>
                    </div>
                </div>
            `;
        }

        function addRow() {
            if (rowCount >= maxRows) {
                return;
            }

            container.insertAdjacentHTML('beforeend', rowTemplate(rowCount));
            rowCount += 1;
            syncControls();
        }

        function syncControls() {
            addBtn.disabled = rowCount >= maxRows;
            const removeButtons = container.querySelectorAll('.remove-row');
            removeButtons.forEach((btn) => {
                btn.disabled = rowCount <= 1;
            });
        }

        addBtn.addEventListener('click', addRow);
        container.addEventListener('click', function(event) {
            if (!event.target.classList.contains('remove-row')) {
                return;
            }

            const row = event.target.closest('.offer-row');
            if (!row) {
                return;
            }

            row.remove();
            rowCount -= 1;
            syncControls();
        });

        addRow();
    })();
</script>
@endsection
