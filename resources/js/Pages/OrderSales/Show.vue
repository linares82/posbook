<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Orden de Compra" sub-title="Ver" />
    </a-col>
    <a-col :span="12">
        <Link href="/orderSales" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-row>
    <a-col :span="24">
        <a-descriptions title="Información" layout="vertical">
            <a-descriptions-item label="Id">{{orderSale.id}}</a-descriptions-item>
            <a-descriptions-item label="Nombre">{{orderSale.fecha}}</a-descriptions-item>
        </a-descriptions>
    </a-col>
</a-row>

<a-table :size="small" bordered :data-source="dataSource" :columns="columns">
    <template #bodyCell="{ column, text, record }">
        <template v-if="column.dataIndex === 'name'">
            <div class="editable-cell">
                    {{ text || ' ' }}
            </div>
        </template>
        <template v-if="column.dataIndex === 'bnd_entrada_registrada'">
            <div class="editable-cell" v-if="text==1">
                    SI
            </div>
        </template>
        <template v-else-if="column.dataIndex === 'operation'">
            <a-popconfirm v-if="dataSource.length && record.bnd_entrada_registrada!=1" title="Esta seguro de proceder?" @confirm="registrarEntrada(record.id)">
                <a>Registrar entrada</a>
            </a-popconfirm>
        </template>
    </template>
</a-table>
</template>

<script>
import Layout from "../../shared/Layout";
import {
    Link
} from "@inertiajs/inertia-vue3";
import {
    computed,
    defineComponent,
    reactive,
    ref
} from 'vue';
import {
    CheckOutlined,
    EditOutlined
} from '@ant-design/icons-vue';
import {
    cloneDeep
} from 'lodash-es';
import {
    Inertia
} from "@inertiajs/inertia";

export default {
    layout: Layout,

    components: {
        Link,
        CheckOutlined,
        EditOutlined,
    },

    props: ["orderSale", "lineas"],

    setup(props) {
        const columns = [{
            title: 'Id',
            dataIndex: 'id',
            width: '10%',
        }, {
            title: 'Plantel',
            dataIndex: 'plantel',
            width: '10%',
        }, {
            title: 'Producto',
            dataIndex: 'product',
            width: '10%',
        }, {
            title: 'Cantidad',
            dataIndex: 'cantidad',
            width: '10%',
        }, {
            title: 'Contacto',
            dataIndex: 'contacto',
            width: '20%',
        }, {
            title: 'Entrada Registrada',
            dataIndex: 'bnd_entrada_registrada',
            width: '20%',
        },{
            title: 'Operacion',
            dataIndex: 'operation',
        }];

        const dataSource = ref(props.lineas);

        const count = computed(() => dataSource.value.length + 1);
        const editableData = reactive({});

        const registrarEntrada = key => {
            //console.log(key)
            //dataSource.value = dataSource.value.filter(item => item.id !== key);
            Inertia.get("/orderSalesLines/receiveOCPlantel/"+key);
        };

        return {
            columns,
            registrarEntrada,
            dataSource,
            editableData,
            count
        };
    },
};
</script>
