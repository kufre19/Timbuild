<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta http-equiv="x-ua-compatible" content="ie=edge" />
  <title>TimBuild Competiton Entries </title>
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <!-- Google Fonts Roboto -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" />
  <!-- MDB -->
  <link rel="stylesheet" href="{{asset('css/mdb.min.css')}}" />
</head>

<body>
  <!-- Start your project here-->
  <div class="container">
    <h2 class='mb-3'>Timbuild Competiton Entries</h2>
    <table id="dtBasicExample" class="table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th class="th-sm">First Name
          </th>
          <th class="th-sm">Last Name
          </th>
          <th class="th-sm">Email
          </th>
          <th class="th-sm">Phone
          </th>
          <th class="th-sm">Region
          </th>
          <th class="th-sm"> Closer Store
          </th>
          <th class="th-sm">Project
          </th>
          <th class="th-sm"> Industry
          </th>
          <th class="th-sm">Connect to Store
          </th>
          <th class="th-sm"> DIY'ER
          </th>
          <th class="th-sm">Contractor
          </th>
        </tr>
      </thead>
      <tbody>
        

          @foreach ($entries as $entry)
          <tr>
            <td>{{$entry->first_name}}</td>
            <td>{{$entry->last_name}}</td>
            <td>{{$entry->email}}</td>
            <td>{{$entry->phone}}</td>
            <td>{{$entry->region}}</td>
            <td>{{$entry->store_closes}}</td>
            <td>{{$entry->project}}</td>
            <td>{{$entry->industry}}</td>
            <td>{{$entry->connect_to_store}}</td>
            <td>{{$entry->is_diy_customer}}</td>
            <td>{{$entry->is_contractor}}</td>
           

          </tr>
          @endforeach
       
        

      </tbody>
      <tfoot>
        <tr>
          <th >First Name
          </th>
          <th >Last Name
          </th>
          <th >Email
          </th>
          <th >Phone
          </th>
          <th >Region
          </th>
          <th > Closer Store
          </th>
          <th >Project
          </th>
          <th> Industry
          </th>
          <th >Connect to Store
          </th>
          <th > DIY'ER
          </th>
          <th >Contractor
          </th>
        </tr>
      </tfoot>
    </table>

    <p><a href="{{url('download')}}" class="btn btn-primary btn-large">Download</a></p>
    <p>
      {{$entries->links()}}
    </p>
  </div>
  <!-- End your project here-->

  <!-- MDB -->
  <script type="text/javascript" src="{{asset('js/mdb.min.js')}}"></script>
  <!-- Custom scripts -->


  <script type="text/javascript">
    $(document).ready(function () {
      $('#dtBasicExample').DataTable();
      $('.dataTables_length').addClass('bs-select');
    });
  </script>
</body>

</html>