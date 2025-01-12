<template>
  <v-container>
    <v-row justify="space-between" align="center">
      <v-col cols="8">
        <h1>Gestion des Employés</h1>
      </v-col>
      <v-col cols="4" class="text-right">
        <v-btn color="primary" @click="openDialog()">
          Ajouter un employé
        </v-btn>
      </v-col>
    </v-row>

    <!-- Search and Filters -->
    <v-row>
      <v-col cols="4">
        <v-text-field v-model="search" label="Rechercher" prepend-icon="mdi-magnify" clearable
          @input="fetchEmployes"></v-text-field>
      </v-col>
      <v-col cols="3">
        <v-select v-model="selectedGrade" :items="grades" label="Filtrer par grade" clearable
          @change="fetchEmployes"></v-select>
      </v-col>
      <v-col cols="3">
        <v-select v-model="selectedUnite" :items="unites" label="Filtrer par unité" clearable
          @change="fetchEmployes"></v-select>
      </v-col>
      <v-col cols="2">
        <v-menu offset-y>
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="primary" v-bind="attrs" v-on="on">
              Exporter
            </v-btn>
          </template>
          <v-list>
            <v-list-item @click="exportData('excel')">
              <v-list-item-title>Exporter en Excel</v-list-item-title>
            </v-list-item>
            <v-list-item @click="exportData('pdf')">
              <v-list-item-title>Exporter en PDF</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-col>
    </v-row>

    <!-- Table des employés -->
    <v-data-table :headers="headers" :items="employes" :loading="loading" :search="search" class="elevation-1">
      <template v-slot:item.actions="{ item }">
        <v-btn icon small @click="openDialog(item)">
          <v-icon>tabler-pencil</v-icon>
        </v-btn>
        <v-btn icon small @click="exportSinglePDF(item)">
          <v-icon>tabler-pdf</v-icon>
        </v-btn>
        <v-btn icon small @click="confirmDelete(item)">
          <v-icon>tabler-trash</v-icon>
        </v-btn>
      </template>
    </v-data-table>

    <!-- Dialog pour ajouter/modifier -->
    <v-dialog v-model="dialog" max-width="1200px">
      <v-card>
        <v-card-title>
          <span>{{ formTitle }}</span>
        </v-card-title>

        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-container>
              <v-row>
                <!-- Informations Personnelles -->
                <v-col cols="12">
                  <h3>Informations Personnelles</h3>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.nom" label="Nom" :rules="[v => !!v || 'Le nom est requis']"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.prenom" label="Prénom" :rules="[v => !!v || 'Le prénom est requis']"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.matricule" label="Matricule"
                    :rules="[v => !!v || 'Le matricule est requis']" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_de_naissance" label="Date de naissance" type="date"
                    required></v-text-field>
                </v-col>

                <!-- Informations Professionnelles -->
                <v-col cols="12">
                  <h3>Informations Professionnelles</h3>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.grade" label="Grade" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.unite" label="Unité" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_de_recrutement" label="Date de recrutement" type="date"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_de_prise_service" label="Date de prise de service" type="date"
                    required></v-text-field>
                </v-col>

                <!-- Formation -->
                <v-col cols="12">
                  <h3>Formation</h3>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.diplome" label="Diplôme" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.type_de_diplome" label="Type de diplôme" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_de_diplome" label="Date d'obtention du diplôme" type="date"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.specialisation" label="Spécialisation" required></v-text-field>
                </v-col>

                <!-- Carrière -->
                <v-col cols="12">
                  <h3>Carrière</h3>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_nomination_actuelle" label="Date de nomination actuelle"
                    type="date" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.decision_de_fonction" label="Décision de fonction"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.cycle_annee" label="Cycle année" type="number"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.duree_fonction" label="Durée de fonction" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.debut" label="Début" type="number" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.fin" label="Fin" type="number" required></v-text-field>
                </v-col>

                <!-- Informations Additionnelles -->
                <v-col cols="12">
                  <h3>Informations Additionnelles</h3>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.situation" label="Situation" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.penalites" label="Pénalités"></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field v-model="editedItem.connaissances" label="Connaissances"></v-text-field>
                </v-col>
              </v-row>
            </v-container>
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="error" text @click="close">Annuler</v-btn>
          <v-btn color="success" text @click="save">Sauvegarder</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Dialog de confirmation de suppression -->
    <v-dialog v-model="dialogDelete" max-width="500px">
      <v-card>
        <v-card-title>Êtes-vous sûr de vouloir supprimer cet employé ?</v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="deleteItem">Confirmer</v-btn>
          <v-btn color="primary" text @click="closeDelete">Annuler</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>



<script>
import axios from 'axios';

export default {
  data() {
    return {
      search: '',
      selectedGrade: null,
      selectedUnite: null,
      grades: [],
      unites: [],
      headers: [
        { title: 'Nom', key: 'nom' },
        { title: 'Prénom', key: 'prenom' },
        { title: 'Matricule', key: 'matricule' },
        { title: 'Grade', key: 'grade' },
        { title: 'Unité', key: 'unite' },
        { title: 'Actions', key: 'actions', sortable: false },
      ],
      employes: [],
      editedItem: {},
      dialog: false,
      dialogDelete: false,
      formTitle: '',
      loading: false,
      valid: false,
    };
  },
  mounted() {
    this.fetchEmployes();
    this.fetchGradesAndUnites();
  },
  methods: {
    async fetchEmployes() {
      this.loading = true;
      try {
        const response = await axios.get('/api/employes', {
          //   params: {
          //     search: this.search,
          //     grade: this.selectedGrade,
          //     unite: this.selectedUnite,
          //   },
        });
        this.employes = response.data;
      } catch (error) {
        console.error('Error fetching employees:', error);
      } finally {
        this.loading = false;
      }
    },
    async fetchGradesAndUnites() {
      try {
        const response = await axios.get('/api/filters');
        this.grades = response.data.grades;
        this.unites = response.data.unites;
      } catch (error) {
        console.error('Error fetching filters:', error);
      }
    },
    openDialog(item = null) {
      this.editedItem = item ? { ...item } : {};
      this.formTitle = item ? 'Modifier un employé' : 'Ajouter un employé';
      this.dialog = true;
    },
    async save() {
      if (!this.$refs.form.validate()) return;
      try {
        if (this.editedItem.id) {
          // Update employee
          await axios.put(`/api/employes/${this.editedItem.id}`, this.editedItem);
        } else {
          // Create new employee
          await axios.post('/api/employes', this.editedItem);
        }
        this.fetchEmployes();
        this.dialog = false;
      } catch (error) {
        console.error('Error saving employee:', error);
      }
    },
    async confirmDelete(item) {
      this.editedItem = { ...item };
      this.dialogDelete = true;
    },
    async deleteItem() {
      try {
        await axios.delete(`/api/employes/${this.editedItem.id}`);
        this.fetchEmployes();
        this.dialogDelete = false;
      } catch (error) {
        console.error('Error deleting employee:', error);
      }
    },
    async exportData(type) {
      try {
        const response = await axios.get(`/api/employes/export/${type}`, { responseType: 'blob' });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `employees.${type}`);
        document.body.appendChild(link);
        link.click();
      } catch (error) {
        console.error('Error exporting data:', error);
      }
    },
    async exportSinglePDF(item) {
      try {
        const response = await axios.get(`/api/employes/${item.id}/export`, { responseType: 'blob' });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `${item.nom}_${item.prenom}.pdf`);
        document.body.appendChild(link);
        link.click();
      } catch (error) {
        console.error('Error exporting PDF:', error);
      }
    },
    close() {
      this.dialog = false;
    },
    closeDelete() {
      this.dialogDelete = false;
    },
  },
};
</script>
<style scoped>
.text-right {
  text-align: end;
}
</style>
