<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Planteles" sub-title="Ver" />
    </a-col>
    <a-col :span="12">
        <Link href="/plantels" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-row>
    <a-col :span="24">
        <a-descriptions title="Información" layout="vertical">
            <a-descriptions-item label="Id">{{plantel.id}}</a-descriptions-item>
            <a-descriptions-item label="Nombre">{{plantel.name}}</a-descriptions-item>
            <a-descriptions-item label="Dirección">{{plantel.address}}</a-descriptions-item>
            <a-descriptions-item label="Teléfono">{{plantel.phone}}</a-descriptions-item>
            <a-descriptions-item label="Director">{{plantel.director}}</a-descriptions-item>
        </a-descriptions>
    </a-col>
</a-row>

<strong>Stock por Producto </strong>

<a-table :columns="columns" :data-source="dataSource" bordered>
    <template #bodyCell="{ column, text, record }">
        <template v-if="['current_stock'].includes(column.dataIndex)">
            <div>
                <a-input v-if="editableData[record.id]" v-model:value="editableData[record.id][column.dataIndex]" style="margin: -5px 0" />
                <template v-else>
                    {{ text }}
                </template>
            </div>
        </template>
        <template v-else-if="column.dataIndex === 'operation'">
            <div class="editable-row-operations">
                <span v-if="editableData[record.id]">
                    <a-typography-link @click="save(record.id)">Guardar</a-typography-link> 
                    <a-divider type="vertical" style="background-color: #9AA2C2" />
                    <a-popconfirm title="Estas seguro de cancelar?" @confirm="cancel(record.id)">
                        <a>Cancelar</a>
                    </a-popconfirm>
                </span>
                <span v-else>
                    <a @click="edit(record.id)">Editar</a>
                </span>
            </div>
        </template>
    </template>
</a-table>
</template>

<script>
import Layout from "../../shared/Layout";
import {
    Inertia
} from "@inertiajs/inertia";
import {
    Link
} from "@inertiajs/inertia-vue3";
import {
    cloneDeep
} from 'lodash-es';
import debounce from "lodash/debounce";
import {
    defineComponent,
    reactive,
    ref
} from 'vue';
import {
    SmileOutlined,
    SearchOutlined,
    InfoCircleOutlined,
    FormOutlined,
    DeleteOutlined,
    EyeOutlined
} from "@ant-design/icons-vue";

const columns = [{
    title: 'Id',
    dataIndex: 'id',
    width: '25%',
}, {
    title: 'Producto',
    dataIndex: 'producto',
    width: '25%',
}, {
    title: 'Stock Actual',
    dataIndex: 'current_stock',
    width: '40%',
}, {
    title: 'Operacion',
    dataIndex: 'operation',
}];

export default {
    layout: Layout,

    components: {
        Link
    },

    props: ["plantel", "stocks"],

    setup(props) {
        const dataSource = ref(props.stocks);
        const editableData = reactive({});

        const edit = key => {
            editableData[key] = cloneDeep(dataSource.value.filter(item => key === item.id)[0]);
        };

        const save = key => {
            //console.log(editableData[key].current_stock);
            Object.assign(dataSource.value.filter(item => key === item.id)[0], editableData[key]);

            Inertia.post(
                "/stocks/edit/" + editableData[key].id, {
                    current_stock: editableData[key].current_stock
                }, {
                    preserveState: true,
                    replace: true,
                }
            );

            delete editableData[key];
        };

        const cancel = key => {
            delete editableData[key];
        };

        return {
            dataSource,
            columns,
            editingKey: '',
            editableData,
            edit,
            save,
            cancel,
        };
    }
};
</script>
