@extends('layout/template')

@section('content')
 <h1>Registration</h1>
 <a href="{{url('/registration/create')}}" class="btn btn-success">Create Account</a>
 <hr>
 <table class="table table-striped table-bordered table-hover">
     <thead>
     <tr class="bg-info">
         <th>username</th>
         <th>First Name</th>
         <th>Last Name</th>
         <th>Email</th>
         <th>Gender</th>
         <th>Age</th>
         <th>Address</th>
         <th>Created</th>

         <th colspan="3">Actions</th>
     </tr>
     </thead>
     <tbody>
     @foreach ($regs as $reg)
         <tr>
             <td>{{ $reg->username }}</td>
             <td>{{ $reg->first_name }}</td>
             <td>{{ $reg->last_name }}</td>
             <td>{{ $reg->email }}</td>
             <td>{{ $reg->gender }}</td>
             <td>{{ $reg->age }}</td>
             <td>{{ $reg->address }}</td>
             <td>{{ $reg->created }}</td>

             <td><a href="{{url('registration',$reg->id)}}" class="btn btn-primary">Read</a></td>
             <td><a href="{{route('registation.edit',$reg->id)}}" class="btn btn-warning">Update</a></td>
             <td>
             {!! Form::open(['method' => 'DELETE', 'route'=>['books.destroy', $reg->id]]) !!}
             {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
             {!! Form::close() !!}
             </td>
         </tr>
     @endforeach

     </tbody>

 </table>
@endsection