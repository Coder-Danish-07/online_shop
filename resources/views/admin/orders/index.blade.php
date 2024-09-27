@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Orders</h1>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        @include('admin.message')
        <div class="card">
            <form action="{{ route('orders.index')}}" method="get">
            <div class="card-header">
                <div class="card-title">
                    <button type="button" onclick="window.location.href='{{ route("orders.index") }}'" class="btn btn-default btn-sm">Reset</button>
                </div>
                <div class="card-tools">
                    <div class="input-group input-group" style="width: 250px;">
                        <input type="text" name="keyword" value="{{ Request::get('keyword') }}" class="form-control float-right" placeholder="Search">
    
                        <div class="input-group-append">
                          <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                          </button>
                        </div>
                      </div>
                </div>
            </div>
            </form>
            <div class="card-body table-responsive p-0">								
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Orders#</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Purchase Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($orders->isNotEmpty())
                        @foreach ($orders as $order)
                        <tr>
                            <td><a href="{{route('orders.detail',$order->id)}}">{{ $order->id }}</a></td>
                            {{-- <td>{{ $loop->iteration }}</td> --}}
                            <td>{{ $order->name }}</td>
                            <td>{{ $order->email }}</td>
                            <td>{{ $order->mobile }}</td>
                            @if($order->status == 'pending')
                            <td><span class="badge bg-danger">Pending</span></td>
                            @elseif($order->status == 'shipped')
                            <td><span class="badge bg-info">Shipped</span></td>
                            @else
                            <td><span class="badge bg-success">Delivered</span></td>
                            @endif
                            <td>{{number_format($order->grand_total,2)}}</td>
                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M, Y')}}</td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="5">Orders Not Found</td>
                        </tr>
                        @endif 
                    </tbody>
                </table>										
            </div>
            <div class="card-footer clearfix">
                <ul class="pagination pagination m-0 float-right">
                {{ $orders->links() }}
                </ul>
            </div>
        </div>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->
@endsection

@section('customJS')   
@endsection