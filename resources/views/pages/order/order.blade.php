@extends('layout.master')

@push('plugin-styles')
@endpush

@section('content')


<link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">




<a href="{{route('orders.create')}}" class="btn btn-primary"  ><i class="fas fs-plus"></i> add orders</a>

<table id="myTable" class="display">
    <thead>
        <tr>
            <th>id</th>
            <!-- <th>name</th> -->
            <th>order number</th>
            <th>price</th>
            <th>table number</th>
             <th>status</th>
            <th>action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $or)
        <tr>
            <td>{{$or->id}}</td>
            
            <td>{{$or->order_number}}</td>
            <td>{{$or->total_price}}</td>
            <td>{{$or->table->number_table ??0}}</td>
            <td>{{$or->status}}</td>
            
            <td>

                <a href="{{ route('orders.edit', $or->id) }}" class="btn btn-success"><i class="fas fa-edit"></i> Update</a>
    
                 <a href="{{ route('orders.delete', $or->id) }}" class="btn btn-danger">
    <i class="fas fa-trash"></i> Delete
</a>

                <!-- <a href="" class="btn btn-dark"><i class="fas fa-image"></i> اضافه صوره</a>
  -->

            </td>
        </tr>
        @endforeach
    </tbody>
</table>


@endsection

@section('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
integrity="sha512-MXe5EK5gyK+fbhwQy/dukwz9fw71HZcsM4KsyDBDTvMyjymkiO0M5qqU0lF4vqLI4VnKf1+DIKf1GM6RFkO8PA=="
crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"> </script>

<script>
    $(document).ready( function () {
    let table = new DataTable('#myTable');

});
</script>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/chart.js') }}"></script>
@endpush
