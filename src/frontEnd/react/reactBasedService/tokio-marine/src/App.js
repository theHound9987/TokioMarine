
import 'bootstrap/dist/css/bootstrap.min.css';
import {NotesRepository} from "./repositories/NotesRepository";
import {useEffect, useState} from "react";
import {TagRepository} from "./repositories/TagRepository";
import {Button, Card, Col, Container, Form, InputGroup, Modal, Row, Tab, Tabs} from "react-bootstrap";
import {Notes} from './components/Notes'
import {Tags} from "./components/Tags";

function App() {

  const [onRunNotes,setOnRunNotes] = useState(0);
  const [onRunTags,setOnRunTags] = useState(0);
  let noteRepository = new NotesRepository();
  let tagRepository = new TagRepository();
  const [notes, setNotes] = useState(null);
  const [tags, setTags] = useState(null);
  const [showAddNote,setShowAddNote] = useState(false)

  const [validatedAddNote, setValidatedAddNote] = useState(false);
  const [validatedAddTag, setValidatedAddTag] = useState(false);

  const [noteTitle, setNoteTitle] = useState('');
  const [noteDescription, setNoteDescription] = useState('');
  const [tagsField, setTagsField] = useState([])
  const [tagName, setTagName] = useState('');



  const handleShowAddNote = () => {
    setShowAddNote(true)
  }

  const handleAddNoteClose = () => {
    setShowAddNote(false)
  }

  const handleSubmitNote = async (event) => {
    const form = event.currentTarget;
    if (form.checkValidity() === false) {
      event.preventDefault();
      event.stopPropagation();
    }

    let response = await fetch("http://laravel.docker.localhost/api/notes",{
      headers : {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      method: "POST",
      body: JSON.stringify({
        title: noteTitle,
        description: noteDescription,
        tags: tagsField
      }),
    })

    let resJson = await response.json();
    console.log(resJson)
    if (response.status === 200) {
      setNoteTitle("");
      setNoteDescription("");
      setTagsField([]);
    }
    setOnRunNotes(onRunNotes+1)

    setValidatedAddNote(true);
    setShowAddNote(false)
  }

  const handleSubmitTag = async (event) => {
    const form = event.currentTarget;
    if (form.checkValidity() === false) {
      event.preventDefault();
      event.stopPropagation();
    }

    let response = await fetch("http://laravel.docker.localhost/api/tags",{
      headers : {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      method: "POST",
      body: JSON.stringify({
        name: tagName,
        dasdfa:"",
      }),
    })

    let resJson = await response.json();
    if (response.status === 200) {
      setTagName("");
      setOnRunTags(onRunTags +1)

      setValidatedAddTag(true);
    }

  }

  useEffect(() => {
    noteRepository.all().then((response) =>{
      setNotes(response)
    })
  },[onRunNotes])

  useEffect(() => {
    tagRepository.all().then((response) =>{
      setTags(response)
    })
  },[onRunTags])

  let jsxNotes = null
  let jsxTags = null
  let jaxOptionTags = null

  if(notes){
    jsxNotes = <Notes notes={notes}/>
  }

  if (tags){
    jsxTags = <Tags tags={tags}/>
    jaxOptionTags = tags.map((tag) =>{
      return <option key={tag.id} value={tag.id}>{tag.name}</option>
    })
  }

  return (
    <Container>
      <Modal show={showAddNote} onHide={handleAddNoteClose}>
        <Modal.Header closeButton>
          <Modal.Title>Add Note</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <Form noValidate validated={validatedAddNote} onSubmit={handleSubmitNote}>
            <Form.Group>
              <Form.Label>Title</Form.Label>
              <Form.Control required type="text" placeholder="Enter Title" value={noteTitle} onChange={(e) => { setNoteTitle(e.target.value)}}/>
              <Form.Control.Feedback type="invalid">
                Please provide a valid Title.
              </Form.Control.Feedback>
            </Form.Group>
            <Form.Group>
              <Form.Label>Description</Form.Label>
              <Form.Control required as="textarea" placeholder="Enter Description" value={noteDescription} onChange={(e) =>{  setNoteDescription(e.target.value)}}/>
              <Form.Control.Feedback type="invalid">
                Please provide a valid Description.
              </Form.Control.Feedback>
            </Form.Group>
            <Form.Group as={Col} controlId="my_multiselect_field">
              <Form.Label>Tags</Form.Label>
              <Form.Control required as="select" multiple value={tagsField} onChange={e => setTagsField([].slice.call(e.target.selectedOptions).map(item => item.value))}>
                {jaxOptionTags}
              </Form.Control>
              <Form.Control.Feedback type="invalid">
                Please provide a valid Tag.
              </Form.Control.Feedback>
            </Form.Group>
          </Form>
        </Modal.Body>
        <Modal.Footer>
          <Button onClick={handleSubmitNote}>Submit</Button>
        </Modal.Footer>
      </Modal>
      <Row>
        <Tabs defaultActiveKey="notes">
          <Tab title="Notes" eventKey="notes">
            <Card>
              <Card.Body>
                <Button onClick={handleShowAddNote}>Add Note</Button>
              </Card.Body>
            </Card>
            {jsxNotes}
          </Tab>
          <Tab title="Tags" eventKey="tags">
            <Card>
              <Card.Body>
                <Form noValidate validated={validatedAddTag} onSubmit={handleSubmitTag}>
                <InputGroup className="mb-3">
                  <Form.Control.Feedback type="invalid">
                    Please provide a name.
                  </Form.Control.Feedback>
                  <Form.Control
                      required
                      placeholder="Name"
                      aria-label="Name"
                      aria-describedby="basic-addon2"
                      value={tagName || ""}
                      onChange={(e) => setTagName(e.target.value)}
                  />
                  <Button  type="submit" variant="outline-secondary" id="button-addon2">
                    Submit
                  </Button>
                </InputGroup>
                </Form>
              </Card.Body>
            </Card>
            {jsxTags}
          </Tab>
        </Tabs>
      </Row>
    </Container>
  );
}

export default App;
