// assets/react/components/HomeComponent.jsx
import React from 'react';
import {useState, useEffect} from 'react';
import axios from "axios";

import  Loading  from "./Loading";
import  NoteCardComponent from "./NoteCardComponent";

const HomeComponent = () => {
    //Variables et Fonctions du composant
    //Gestion des erreurs
    const [errMsg, setErrMsg] = useState('');
    //Variable notes (tableau vide) -> stockage des notes récupérées par axios  
    const [notes, setNotes] = useState([]);
    //Variable initialement vide se remplie si le champs texte est modifié (recherche)
    const [inputSearch, setInputSearch] = useState("");
    //Variable de chargement
    const [loading,setLoading] = useState(false);

    //Requète vers API
    const getNotes = () => {
        setLoading(true);
        //https://www.npmjs.com/package/dotenv-webpack
        axios.get(process.env.REACT_APP_URL+`notes`).catch(
           function (error) {
               if (error.response) {
                
                setErrMsg(error.response.data.message);

               } 
             }
       )
       //On charge dans users via setUsers
       .then(
           (res) => {
              
              setNotes(res.data['hydra:member']);
              setErrMsg('');
              setLoading(false)
             
           }
       )
   }
    // Le useEffect se joue lorsque le composant est monté au chargement de la page
    // Ici on lance la fonction getNotes
    useEffect(() => getNotes(),[inputSearch])

    return (
        <>
        
            <div className="container text-center bg-indigo-950">

                <div className={ errMsg ? "errmsg alert alert-warning alert-dismissible fade show" : "d-none"} role="alert">
                    <strong>Holy guacamole!</strong> {errMsg}
                    <button type="button" className="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                
                <h1 className="text-3xl">
                  Carnet de notes Pyman
                </h1>

                <div className="row">
                    <div className="col-md-4"></div>
                    <div className="input-group mb-3 col-md-4">
                        <span className="input-group-text"><i className="text-slate-50 fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" className="form-control text-slate-50" onChange={(e) => setInputSearch(e.target.value)}/>
                    </div>
                    <div className="col-md-4"></div>
                </div>
                
                <div className="row">
                    
                    {
                         loading ? <Loading/> :
                            notes &&
                                notes.map(
                                   (note) => <NoteCardComponent key={note.id} data={note}/>
                                  
                                )
                    }
        
                </div>
            </div>
        </>
    );
}

export default HomeComponent;
