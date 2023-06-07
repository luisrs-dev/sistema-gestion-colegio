import React, { useContext, useEffect, useState } from "react";
import { RegistroNotasContext } from "../context/RegistroNotasContext";
import Row from "./Row";
import { log } from "neo-async";
import axios from 'axios';

export function Table({cursoAsignatura}) {
  const { alumnos, setAlumnos, notas, setNotas, asignatura, setAsignatura, setProfesor, setCurso } = useContext(RegistroNotasContext);

  useEffect(() => {

    const callDataCurso = async () => {
      try {
        const response = await axios.get(`data-curso-asignatura/${cursoAsignatura}`);
        setNotas(response.data.notas);
        setAlumnos(response.data.alumnos);
        setAsignatura(response.data.asignatura);
        setProfesor(response.data.profesor)
        setCurso(response.data.curso.nombre)
      } catch (error) {
        console.error(error);
      }
    };
    callDataCurso();
  }, []);

  // const {onSubmit} = useContext(RegistroNotasContext)

  if ( alumnos && alumnos.length > 0) {
    return (
      <table className="table table-sm table-bordered ">
        <thead className="bg-primary text-light">
          <tr className="text-center">
            <td colSpan={notas.length * 2 + 2} scope="col">
              Primer Semestre
            </td>
          </tr>
          <tr className="text-center">
            <td rowSpan={2} scope="col">
              Alumno
            </td>
            {notas.map((nota, index) => {
              if (nota && nota.periodo == "Primer Semestre") {
                return (
                  <td colSpan={2} key={index} scope="col">
                    {nota.nombre}
                  </td>
                );
              }
            })}
            <td rowSpan={2} scope="col">
              Nota Final
            </td>
          </tr>
          <tr className="text-center">
            {notas.map((nota, index) => {
              if (nota) {
                return (
                  <>
                    <td key={index} scope="col">
                      Nota
                    </td>
                    <td key={index + 1} scope="col">
                      {nota.porcentaje}%
                    </td>
                  </>
                );
              }
            })}
          </tr>
        </thead>
        <tbody>
          {alumnos.length > 0 ? alumnos.map((alumno, index) => (
            <Row key={alumno.id} alumno={alumno} notasTemplate={notas} asignatura={asignatura.id} />
          ) ) : 'No hay alumnos en el curso' }
        </tbody>
      </table>
    );
  } else {
    return "Curso no asignado";
  }
}
