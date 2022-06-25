@extends('customLayout.layout')

@section('title', 'Invoice')

<!-- navbar -->

@section('navbar')
<nav class="navbar navbar-expand-lg navbar-dark bg-dark w-100">
    {{-- <a href="{{ route('view.project', ['id' => $projectID]) }}" class="nav-link text-light"><i class="bi bi-arrow-left-circle"></i></a> --}}
    <a class="navbar-brand" href="#">Admin | <small>Invoice Details</small></a>
    <a href="{{ route('adminlogout') }}" class="nav-link ml-auto text-light">Logout</a>
</nav>
@endsection

@section('main-content')
<div class="row">
    <div class="col-3 d-flex flex-column offset-3">
        <h4>iExtend Labs</h4>
        <small class="d-block">Gulberg-3 Lahore, Pakistan</small>
        <small class="d-block">Phone :- 0300 - 000000-0</small>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-5 offset-md-3">   
        <div class="col-md-12">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th>Task Description</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $item)
                        <tr>
                            <td>{{ $item->task_desc }}</td>
                            <td>{{ $item->date }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>        
</div>

<div class="row my-4">
    <div class="col-md-10 offset-md-1">
        <table class="table">
            <thead class="bg-success text-light">
                <tr> 
                    <th>Project Name</th>
                    <th>Invoice Title</th>
                    <th>Date Created</th>
                    @if($invoice->start_date !== NULL)
                    <th>Start Date</th>
                    <th>End Date</th>
                    @endif
                    <th>Total Hours</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
               <tr>
                   <td>{{ $invoice->project_name }}</td>
                   <td>
                       @if ($invoice->invoice_title )                           
                       {{ $invoice->invoice_title }}
                        @else 
                        Null
                       @endif
                    </td>
                   <td>{{ $invoice->created_at->format('d-M-Y') }}</td>
                   @if($invoice->start_date !== NULL)
                   <td>{{ $invoice->start_date }}</td>
                   <td>{{ $invoice->end_date }}</td>
                   @endif
                   <td>{{ $invoice->total_hours }}</td>
                   <td>${{ $invoice->invoice_rate }}</td>
               </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
