export class NotesRepository{
    all(){
        return fetch("http://laravel.docker.localhost/api/notes", {
            headers : {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }

        }).then((response) => {
            return response.json();
        }).then((data) => {return data.data})
            .catch((error) => console.log(error));
    }
}