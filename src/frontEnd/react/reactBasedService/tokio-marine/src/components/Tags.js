
import 'bootstrap/dist/css/bootstrap.min.css';
import {Card} from "react-bootstrap";

export function Tags(props){

    const jsxTags = props.tags.map((tag) => {
        return (

            <Card key={tag.id}>
                <Card.Body>
                    <Card.Title >{tag.name}</Card.Title>
                </Card.Body>
            </Card>
        )
    });
    return jsxTags;
}