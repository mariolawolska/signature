<div style="padding: 30px 0;  font-weight: bold;"> <h3>LIST OF LAST 5 ADDED COMMENTS</h3></div>
<table class="table">
    <thead>
        <tr>
            <th scope="col">#1</th>
            <th scope="col">NAME</th>
            <th scope="col">EMAIL</th>
            <th scope="col">COMMENT</th>
            <th scope="col">UPDATED</th>
            <th scope="col">CREATED</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($persons as $person)
        <tr>
            <td>{{$person->id}}</td>
            <td>{{$person->name}}</td>
            <td>{{$person->email}}</td>
            <td>{{$person->comment}}</td>
            <td>{{$person->updated_at}}</td>
            <td>{{$person->created_at}}</td>
            <td><button  personId="{{ $person->id }}" class="btn btn-danger deleteCommentForm" style="border-radius: 50%;">Delete</button></td>
        </tr>
        @endforeach
    </tbody>
</table>
