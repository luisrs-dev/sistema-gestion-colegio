import React, { useState, useEffect, createContext } from "react";
import Swal from "sweetalert2";
import axios from "axios";
import download from "downloadjs";
import { log } from "neo-async";

import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

export const RegistroNotasContext = createContext();

export function RegistroNotasContextProvider(props) {
  const [alumnos, setAlumnos] = useState(null);
  const [notas, setNotas] = useState([]);
  const [asignatura, setAsignatura] = useState(null);
  const [profesor, setProfesor] = useState(null);
  const [curso, setCurso] = useState(null);


  function successToast(text) {
    toast.success(text, {
      position: "top-right",
      autoClose: 3000,
      hideProgressBar: false,
      closeOnClick: true,
      pauseOnHover: true,
      draggable: true,
      progress: undefined,
      theme: "light",
    });
  }

  function errorToast(text) {
    toast.error(text, {
      position: "top-right",
      autoClose: 3000,
      hideProgressBar: false,
      closeOnClick: true,
      pauseOnHover: true,
      draggable: true,
      progress: undefined,
      theme: "light",
    });
  }

  return (
    <RegistroNotasContext.Provider
      value={{
        alumnos,
        setAlumnos,
        successToast,
        errorToast,
        notas,
        setNotas,
        asignatura,
        setAsignatura,
        profesor, setProfesor,
        curso, setCurso
      }}
    >
      {props.children}
      <ToastContainer />
    </RegistroNotasContext.Provider>
  );
}

export default RegistroNotasContext;
