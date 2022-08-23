<template>
  <a-layout-header
    style="background: #6a05ca; color: floralwhite; fontsize: 2em"
  >
    <a-row>
      <a-col :span="8">
        <a-divider type="vertical" style="background-color: #fff" />
        Bienvenido Invitado
      </a-col>
      <a-col :span="8">
        <a-divider type="vertical" style="background-color: #fff" />
        <strong>NombreApp</strong>
      </a-col>
      <a-col :span="8"> </a-col>
    </a-row>
  </a-layout-header>
  <slot></slot>
</template>
<script>
</script>
