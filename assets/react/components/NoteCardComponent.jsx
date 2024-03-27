import React from "react";


const NoteCardComponent = ({data}) => {

    return(
        <>
    


        <div className="max-w-sm rounded overflow-hidden shadow-lg">
           
            <div className="px-6 py-4">
                <div className="font-bold text-xl mb-2">{data.title}</div>
                <p className="text-gray-700 text-base">
                {data.description}
                </p>
            </div>
            <div className="px-6 py-4">
               
            </div>
            <div className="px-6 py-4">
                <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Modifier
                </button>
                <button className="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2">
                Supprimer
                </button>
            </div>
        </div>
        
            
        
        </>
    )
}

export default NoteCardComponent