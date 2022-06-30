export class TagRepository{
    all(){
        return fetch("http://laravel.docker.localhost/api/tags", {
            headers : {
                'Content-Type': 'application/json',
                'Accept': 'application/json',

            }

        }).then((response) => {
            return response.json();
        }).then((data) => {return data.data}).catch((error) => console.log(error));
    }
}