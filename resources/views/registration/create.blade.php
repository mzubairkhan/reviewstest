@extends('layout.template')
@section('content')
    <h1>Registration</h1>
    <form action="" method="POST">
        <div class="form-group">
          <label for="Username">Username:</label>
          <input class="form-control" name="username" type="text">
        </div>

        <div class="form-group">
          <label for="First Name">First Name:</label>
          <input class="form-control" name="first_name" type="text">
        </div>
        <div class="form-group">
          <label for="Last Name">Last Name:</label>
          <input class="form-control" name="last_name" type="text">
        </div>
        <div class="form-group">
          <label for="Email">Email:</label>
          <input class="form-control" name="email" type="text">
        </div>
        <div class="form-group">
          <label for="Password">Password:</label>
          <input class="form-control" name="password" type="password">
        </div>
        <div class="form-group">
          <label for="Gender">Gender:</label>
            <select name="gender">
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
        <div class="form-group">
          <label for="Age">Age:</label>
            <select name="gender">
                <?php for ($age=18;$age<=60;$age++){?>
                    <option value="<?php echo $age;?>"><?php echo $age;?></option>
               <?php  }?>
            </select>
        </div>
        <div class="form-group">
          <label for="Address">Addres:</label>
          <input class="form-control" name="address" type="textarea">
        </div>

<div class="form-group">
            <input class="btn btn-primary form-control" value="Save" type="submit">
        </div>
    </form>
@stop