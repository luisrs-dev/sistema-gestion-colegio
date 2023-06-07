import React, { useContext, useState, useEffect } from "react";
import { RegistroNotasContext } from "../context/RegistroNotasContext";
import { Table } from "./Table";
import axios from "axios";
import download from "downloadjs";
import { Button } from "react-bootstrap";
import {PDFDownloadLink} from '@react-pdf/renderer'
import {DocumentCursoAsignatura} from '../pdfs/DocumentCursoAsignatura'

export const SectionForm = () => {
  const [cursoAsignaturas, setCursoAsignaturas] = useState([]);
  const [cursoAsignatura, setCursoAsignatura] = useState(null);

  const [periodos, setPeriodos] = useState([]);
  const [periodo, setPeriodo] = useState(null);
  const { alumnos, setAlumnos, notas, profesor, curso, asignatura } = useContext(RegistroNotasContext);

  useEffect(() => {
    console.log('profesorId');
    const profesorId = document.getElementById("profesor").value;
    console.log(profesorId);
    const fetchData = async () => {
      try {
        const response = await axios.get(
          `data-profesor/${profesorId}`
        );

        setCursoAsignaturas(response.data.cursoAsignaturas);
        setPeriodos(response.data.periodos);
      } catch (error) {
        console.error(error);
      }
    };
    fetchData();
  }, []);

  function setDataCourse() {
    setAlumnos(null);
    setCursoAsignatura(document.getElementById("cursoAsignatura").value);
    setPeriodo(document.getElementById("periodo").value);
  }

  return (
    <>
      <div className="card shadow col-md-12">
        <div className="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 className="m-0 font-weight-bold text-primary">
            Registro de notas
          </h6>
          {(cursoAsignatura && notas && alumnos) ? (
            <PDFDownloadLink document={<DocumentCursoAsignatura profesor={profesor} notas={notas} curso={curso} asignatura={asignatura} alumnos={alumnos}/>} fileName="reporte-notas-curso.pdf" >
              <Button variant="danger" size="sm">
                <i className="fas fa-download fa-sm text-white"></i> Descargar PDF
              </Button>
            </PDFDownloadLink>
          ) : null}
        </div>
        <div className="card-body">
          <div className="row">
            <div className="col-md-4">
              <label>Asignatura</label>
              <select
                className="form-select form-control"
                aria-label="Default select example"
                id="cursoAsignatura"
                onChange={() => setCursoAsignatura(null)}
              >
                <option disabled>Seleccione una asignatura</option>
                {cursoAsignaturas.map((ca) => (
                  <option key={ca.id} value={ca.id}>
                    {ca.asignatura.nombre} - {ca.curso.nombre}
                  </option>
                ))}
              </select>
            </div>
            <div className="col-md-4">
              <label>Semestre</label>
              <select
                className="form-select form-control"
                aria-label="Default select example"
                id="periodo"
              >
                <option disabled>Seleccione un semestre</option>
                {periodos.map((p) => (
                  <option key={p.id} value={p.id}>
                    {p.nombre}
                  </option>
                ))}
              </select>
            </div>
            <div className="col-md-4 mt-4">
              <button
                onClick={() => cursoAsignatura ? setCursoAsignatura(null) : setDataCourse()}
                className="btn btn-success"
              >
                Ir a registrar
              </button>
            </div>
          </div>
        </div>
        {cursoAsignatura ? <Table cursoAsignatura={cursoAsignatura}/> : null}
        {/* {cursoAsignatura && <Table cursoAsignatura={cursoAsignatura} />} */}
      </div>
    </>
  );
};
