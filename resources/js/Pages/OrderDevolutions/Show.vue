<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="O. Devoluciones" sub-title="Ver" />
    </a-col>
    <a-col :span="12">
        <Link href="/orderDevolutions" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-row>
    <a-col :span="24">
        <a-descriptions title="Información" layout="vertical">
            <a-descriptions-item label="Id">{{orderDevolution.id}}</a-descriptions-item>
            <a-descriptions-item label="Nombre">{{orderDevolution.name}}</a-descriptions-item>
            <a-descriptions-item label="Fecha">{{orderDevolution.fecha}}</a-descriptions-item>
            <a-descriptions-item label="Motivo">{{orderDevolution.motivo}}</a-descriptions-item>
        </a-descriptions>
    </a-col>
</a-row>
<a-row>
    <a-button type="dashed" @click="consultarExistencias"> Consultar Existencias</a-button>
    <a-button type="dashed" @click="crearLineas"> Crear Lineas</a-button>
</a-row>
<a-row>
    <a-col :span="12" v-show="existencias.length>0">
      <div style="background: #475D66; padding: 3px">
        <a-card title="Existencias">
            <table style="table-layout: auto;" class="ant-table-striped">
                <thead class="ant-table-thead">
                    <tr>
                        <th>No.</th>
                        <th>Plantel</th>
                        <th>Producto</th>
                        <th>Existencia</th>
                        <th>Cantidad Devolucion</th>
                    </tr>
                </thead>
                <tbody class="ant-table-tbody">
                    <tr v-for="(existencia, index) in existencias" :key="index">
                        <td>{{index+1}}</td>
                        <td class="ant-table-cell">{{existencia.plantel}}</td>
                        <td class="ant-table-cell">{{existencia.producto}}</td>
                        <td class="ant-table-cell">{{existencia.entrada-existencia.salida}}</td>
                        <td class="ant-table-cell">
                            <a-input v-model:value="existencia.cantidad_devolucion"></a-input>
                        </td>
                    </tr>
                </tbody>
            </table>
        </a-card>
      </div>
    </a-col>
    <a-col :span="12" v-show="lineas.length>0">
        <div style="background: #3C95BE; padding: 3px">
            <a-card title="Lineas">
                <table style="table-layout: auto;" class="ant-table-striped">
                    <thead class="ant-table-thead">
                        <tr>
                            <th>No.</th>
                            <th>Plantel</th>
                            <th>Producto</th>
                            <th>Cantidad Devolucion</th>
                            <th></th>
                        </tr>

                    </thead>
                    <tbody class="ant-table-tbody">
                        <tr v-for="(linea, index) in lineas" :key="index">
                            <td>{{index+1}}</td>
                            <td class="ant-table-cell">{{linea.plantel}}</td>
                            <td class="ant-table-cell">{{linea.product}}</td>
                            <td class="ant-table-cell">{{linea.cantidad}}</td>
                            <td>
                                <a-popconfirm title="Estas seguro de la operación?" ok-text="Si" cancel-text="No" @confirm="removeLinea(linea)">
                                    <MinusCircleOutlined /> Eliminar
                                </a-popconfirm>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </a-card>
        </div>
    </a-col>
</a-row>
</template>

<script>
import Layout from "../../shared/Layout";
import {
    Inertia
} from "@inertiajs/inertia";
import {
    Link
} from "@inertiajs/inertia-vue3";
import axios from 'axios';
import {
    defineComponent,
    reactive,
    ref,
    watch,
    watchEffect
} from "vue";
import {
    MinusCircleOutlined,
} from "@ant-design/icons-vue";

export default {
    layout: Layout,

    components: {
        Link,
        MinusCircleOutlined
    },

    props: ["orderDevolution", 'route_consultaExistencias', 'route_storeLines', 'orderDevolutionLines', 'route_destroy_ln'],

    setup(props) {
        let existencias = reactive([]);
        let lineas = reactive([]);

        for (let linea in props.orderDevolutionLines) {
            lineas.push({
                id: props.orderDevolutionLines[linea].id,
                order_devolution_id: props.orderDevolutionLines[linea].order_devolution_id,
                plantel_id: props.orderDevolutionLines[linea].plantel_id,
                plantel: props.orderDevolutionLines[linea].plantel,
                product_id: props.orderDevolutionLines[linea].product_id,
                product: props.orderDevolutionLines[linea].product,
                cantidad: props.orderDevolutionLines[linea].cantidad,
                bnd_salida_registrada: props.orderDevolutionLines[linea].bnd_salida_registrada
            });
        }

        console.log(lineas);

        const consultarExistencias = () => {
            //console.log(props.route_consultaExistencias);
            axios.get(props.route_consultaExistencias)
                .then(response => {
                    //console.log(response.data.existencias);
                    for (let existencia in response.data.existencias) {
                        //console.log(response.data.existencias[existencia]);
                        existencias.push({
                            id: response.data.existencias[existencia].id,
                            plantel: response.data.existencias[existencia].plantel,
                            plantel_id: response.data.existencias[existencia].plantel_id,
                            producto: response.data.existencias[existencia].producto,
                            producto_id: response.data.existencias[existencia].producto_id,
                            entrada: response.data.existencias[existencia].cantidad_entrada,
                            salida: response.data.existencias[existencia].cantidad_salida,
                            cantidad_devolucion: 0,
                            order_devolution_id: props.orderDevolution.id
                        });
                    }
                    //console.log(existencias);
                })
        };

        const crearLineas = () => {
            //console.log(existencias);
            let revision_cantidades = 0;
            for (let existencia in existencias) {
                if ((existencias[existencia].entrada - existencias[existencia].salida) < existencias[existencia].cantidad_devolucion) {
                    revision_cantidades++;
                }
            }
            if (revision_cantidades > 0) {
                alert(`Existen ${revision_cantidades} registros con devoluciones mayores a la existencia`);
            } else {
                axios.post(props.route_storeLines, existencias)
                    .then(response => {
                      location.reload();
                    })
            }

            //Inertia.post(props.route_storeLines, existencias);
        };

        const removeLinea = item => {
            //console.log(item);
            Inertia.delete(props.route_destroy_ln + "?id=" + item.id, null, {
                onStart: () => {
                    processingLinea.value = true;
                },
                onFinish: () => {
                    processingLinea.value = false;
                    location.reload();
                },
            })
            location.reload();
        };

        return {
            consultarExistencias,
            existencias,
            crearLineas,
            lineas,
            removeLinea
        };
    }

};
</script>
