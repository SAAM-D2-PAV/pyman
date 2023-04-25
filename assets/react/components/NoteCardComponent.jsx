import React from "react";


const NoteCardComponent = ({data}) => {

    return(
        <>
       <div className="card text-slate-50" >
            <div className="card-body">
                <h5 className="card-title">{data.title}</h5>
                <p className="">{data.description}</p>
                <a href="#" className="btn btn-primary">Go </a>
            </div>
        </div>
        
            
        
        </>
    )
}

export default NoteCardComponent