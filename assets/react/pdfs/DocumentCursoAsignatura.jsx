import React, { useContext } from "react";
import { RegistroNotasContext } from "../context/RegistroNotasContext";
import { StyleSheet, Document, Page, Text, View } from "@react-pdf/renderer";

export const DocumentCursoAsignatura = ({
  profesor,
  curso,
  asignatura,
  notas,
  alumnos,
}) => {

  const styles = StyleSheet.create({
    table: {
      display: "table",
      width: "auto",
      borderStyle: "solid",
      borderWidth: 1,
      borderRightWidth: 0,
      borderBottomWidth: 0,
    },
    tableRow: {
      margin: "auto",
      flexDirection: "row",
    },
    tableColName: {
      width: "15%",
      borderStyle: "solid",
      borderWidth: 1,
      borderLeftWidth: 0,
      borderTopWidth: 0,
    },
    tableCol: {
      width: "14%",
      borderStyle: "solid",
      borderWidth: 1,
      borderLeftWidth: 0,
      borderTopWidth: 0,
    },
    tableCell: {
      margin: "auto",
      marginTop: 5,
      marginBottom: 5,
      fontSize: 8,
    },
    text : {
      marginTop : '5%',
      fontSize : 10,
      marginLeft : '5%'
    },
    body: { 
      padding: "20", 
      fontSize: 8
    }
  });

  const nombreNota = {
    Formativa: "Form.",
    Sumativa: "Sum.",
  };

  return (
    <Document>
      <Page style={styles.body}>
      {/* <Page style={styles.body} orientation="landscape"> */}
        <View style={styles.text}>
          <Text>Profesor: {profesor}</Text>
          <Text>Curso: {curso}</Text>
          <Text>Asignatura: {asignatura.nombre}</Text>
        </View>
        <View style={styles.table}>
          <View style={[styles.tableRow]}>
            <View style={styles.tableColName}>
              <Text style={styles.tableCell}>Alumno</Text>
            </View>

            {notas.map((nota) => (
              <View style={styles.tableCol}>
                <Text style={styles.tableCell}>
                  {nombreNota[nota.nombre]} {nota.porcentaje}%
                </Text>
              </View>
            ))}

            <View style={styles.tableCol}>
              <Text style={styles.tableCell}>Nota Final</Text>
            </View>
          </View>

          {alumnos.map((alumno) => (
            <View style={[styles.tableRow]}>
              <View style={styles.tableColName}>
                <Text style={styles.tableCell}>{alumno.nombre.trim()}</Text>
              </View>

              {notas.map((nota) => (
                <View style={styles.tableCol}>
                  <Text style={styles.tableCell}>
                    {alumno.notas.find(
                      (notaAlumno) => notaAlumno.notaTemplate == nota.id
                    )
                      ? alumno.notas.find(
                          (notaAlumno) => notaAlumno.notaTemplate == nota.id
                        ).calificacion
                      : "-"}
                  </Text>
                </View>
              ))}
              <View style={styles.tableCol}>
                <Text style={styles.tableCell}> </Text>
              </View>

            </View>
          ))}
          
        </View>
      </Page>
    </Document>
  );
};
