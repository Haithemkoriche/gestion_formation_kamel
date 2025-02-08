<template>
  <v-container>
    <h2>{{ $t('Manage Admins') }}</h2>

    <VDataTable :headers="headers" :items="admins" class="mb-5" item-value="id" dense>
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>{{ $t('Admins') }}</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="openCreateDialog">{{ $t('Add Admin') }}</v-btn>
        </v-toolbar>
      </template>
      <template v-slot:[`item.actions`]="{ item }">
        <v-btn icon color="blue" @click="openEditDialog(item)">
          <VIcon>tabler-edit</VIcon>
        </v-btn>
        <v-btn icon color="red" @click="deleteAdmin(item.id)">
          <v-icon>tabler-trash</v-icon>
        </v-btn>
      </template>
    </VDataTable>

    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title>
          <span v-if="editMode">{{ $t('Edit Admin') }}</span>
          <span v-else>{{ $t('Add Admin') }}</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="form">
            <VTextField class="mb-4" v-model="form.username" :label="$t('UserName')" required></VTextField>
            <!-- <VTextField class="mb-4" v-model="form.email" :label="$t('Email')" required></VTextField> -->
            <VTextField class="mb-4" v-if="!editMode" v-model="form.password" :label="$t('Password')" type="password"
              required>
            </VTextField>
            <v-select v-model="form.permissions" :items="permissions" item-title="name" item-value="name"
              :label="$t('Assign Permissions')" multiple></v-select>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="dialog = false">{{ $t('Cancel') }}</v-btn>
          <v-btn color="success" @click="submitForm">{{ editMode ? $t('Update') : $t('Create') }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>


<script>
import axios from "axios";

export default {
  data() {
    return {
      admins: [],
      permissions: [],
      dialog: false,
      editMode: false,
      form: {
        id: null,
        username: "",
        // email: "",
        password: "", // Added password field
        permissions: [],
      },
      headers: [
        { title: "UserName", value: "username" },
        // { title: "Email", value: "email" },
        { title: "Actions", value: "actions", sortable: false },
      ],
    };
  },
  methods: {
    async fetchAdmins() {
      const response = await axios.get("/api/admins");
      this.admins = response.data;
    },
    async fetchPermissions() {
      const response = await axios.get("/api/permissions");
      this.permissions = response.data;
    },
    openCreateDialog() {
      this.dialog = true;
      this.editMode = false;
      this.form = { id: null, username: "", password: "", permissions: [] };
    },
    openEditDialog(admin) {
      this.dialog = true;
      this.editMode = true;
      this.form = { ...admin, permissions: admin.permissions.map((p) => p.username) };
    },
    async submitForm() {
      if (!this.form.username || (!this.editMode && !this.form.password)) {
        alert("Please fill all required fields.");
        return;
      }
      try {
        if (this.editMode) {
          await axios.put(`/api/admins/${this.form.id}`, {
            username: this.form.username,
            email: this.form.email,
            permissions: this.form.permissions,
          });
        } else {
          await axios.post("/api/admins", this.form);
        }
        this.dialog = false;
        this.fetchAdmins();
      } catch (error) {
        console.error("Error saving admin:", error.response.data);
      }
    },
    async deleteAdmin(id) {
      try {
        await axios.delete(`/api/admins/${id}`);
        this.fetchAdmins();
      } catch (error) {
        console.error("Error deleting admin:", error.response.data);
      }
    },
  },
  mounted() {
    this.fetchAdmins();
    this.fetchPermissions();
  },
};
</script>
