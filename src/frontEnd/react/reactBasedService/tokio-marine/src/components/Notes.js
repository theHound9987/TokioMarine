
import 'bootstrap/dist/css/bootstrap.min.css';
import {Badge, Card} from "react-bootstrap";

export function Notes(props){

    const jsxNotes = props.notes.map((note) => {
        const tags = note.tags.map((tag) => {
            const tempKey = note.id +"|"+ tag.id
            return (
                <Badge key={tempKey}>{tag.name}</Badge>
            )
        })
        return (

            <Card key={note.id}>
                <Card.Body>
                    <Card.Title >{note.title} -- {note.create_at}</Card.Title>
                    <Card.Text>
                        {note.description}<p/>
                        {tags}
                    </Card.Text>
                </Card.Body>
            </Card>
        )
    });
    return jsxNotes;
}