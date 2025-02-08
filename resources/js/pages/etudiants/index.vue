<template>
  <v-container>
    <v-row justify="space-between" align="center">
      <v-col cols="8">
        <h1>Gestion des Étudiants</h1>
      </v-col>
      <v-col cols="4" class="text-right">
        <v-btn color="primary" @click="openDialog()">
          Ajouter un Étudiant
        </v-btn>
      </v-col>
    </v-row>

    <!-- Search and Filters -->
    <v-row>
      <v-col cols="4">
        <v-text-field v-model="search" label="Rechercher" prepend-icon="mdi-magnify" clearable
          @input="fetchEtudiants"></v-text-field>
      </v-col>
      <v-col cols="3">
        <v-select v-model="selectedGrade" :items="grades" label="Filtrer par grade" clearable
          @change="fetchEtudiants"></v-select>
      </v-col>
      <v-col cols="3">
        <v-select v-model="selectedUnite" :items="unites" label="Filtrer par unité" clearable
          @change="fetchEtudiants"></v-select>
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

    <!-- Table des étudiants -->
    <v-data-table :headers="headers" :items="etudiants" :loading="loading" :search="search" class="elevation-1">
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
        <!-- Bouton pour convertir l'étudiant en employé -->
        <v-btn icon small @click="convertToEmployee(item)">
          <v-icon>tabler-user-plus</v-icon>
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
                  <v-select v-model="editedItem.sexe" :items="sexOptions" label="Sexe" required></v-select>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_de_naissance" label="Date de naissance" type="date"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.lieu_de_naissance" label="Lieu de naissance"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.pays" label="Pays" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.nationalite" label="Nationalité" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.adresse_actuelle" label="Adresse actuelle" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.situation_familiale" :items="situationFamilialeOptions"
                    label="Situation familiale" required></v-select>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.numero_telephone" label="Numéro de téléphone"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.niveau_scolaire" :items="niveauScolaireOptions" label="Niveau scolaire"
                    required></v-select>
                </v-col>

                <!-- Informations Professionnelles -->
                <v-col cols="12">
                  <h3>Informations Professionnelles</h3>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.grade" :items="gradeOptions" label="Grade" required></v-select>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.matricule" label="Matricule" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.unite" label="Unité" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.nom_entreprise" label="Nom de l'entreprise" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_de_recrutement" label="Date de recrutement" type="date"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.mode_engagement" :items="modeEngagementOptions"
                    label="Mode d'engagement" required></v-select>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_mise_a_niveau" label="Date de mise à niveau" type="date"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_nomination_active" label="Date de nomination active"
                    type="date" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.fonction_actuelle" label="Fonction actuelle"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.annee_scolaire" :items="anneeScolaireOptions" label="Année scolaire"
                    required></v-select>
                </v-col>

                <!-- Formation -->
                <v-col cols="12">
                  <h3>Formation</h3>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.type_formation" :items="typeFormationOptions"
                    label="Type de formation" multiple chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.nature_diplome" :items="natureDiplomeOptions" label="Nature du diplôme"
                    required></v-select>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.specialite" :items="specialiteOptions" label="Spécialité" multiple
                    chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.specialite_partielle" :items="specialitePartielleOptions"
                    label="Spécialité partielle" multiple chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.lieu_formation" label="Lieu de formation" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.annee_formation" :items="anneeFormationOptions"
                    label="Année de formation" multiple chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.duree_formation" label="Durée de formation" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.diplome_obtenu" :items="diplomeObtenuOptions" label="Diplôme obtenu"
                    multiple chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.formations_precedentes" :items="formationsPrecedentesOptions"
                    label="Formations précédentes" multiple chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_obtention_diplome" label="Date d'obtention du diplôme"
                    type="date" required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.diplomes_precedents" :items="diplomesPrecedentsOptions"
                    label="Diplômes précédents" multiple chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.autres_diplomes" :items="autresDiplomesOptions"
                    label="Autres diplômes" multiple chips required></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.cycle" :items="cycleOptions" label="Cycle" required></v-select>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_debut_formation" label="Date de début de formation" type="date"
                    required></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="editedItem.date_fin_formation" label="Date de fin de formation" type="date"
                    required></v-text-field>
                </v-col>

                <!-- Informations Additionnelles -->
                <v-col cols="12">
                  <h3>Informations Additionnelles</h3>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.punition" :items="punitionOptions" label="Punition" multiple
                    chips></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-combobox v-model="editedItem.convalescences" :items="convalescencesOptions" label="Convalescences"
                    multiple chips></v-combobox>
                </v-col>
                <v-col cols="6">
                  <v-select v-model="editedItem.etat_sante" :items="etatSanteOptions" label="État de santé"></v-select>
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
        <v-card-title>Êtes-vous sûr de vouloir supprimer cet Étudiant ?</v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="deleteItem">Confirmer</v-btn>
          <v-btn color="primary" text @click="closeDelete">Annuler</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';

// Réactifs
const search = ref('');
const selectedGrade = ref(null);
const selectedUnite = ref(null);
const grades = ref([]);
const unites = ref([]);
const headers = ref([
  { title: 'Nom', key: 'nom' },
  { title: 'Prénom', key: 'prenom' },
  { title: 'Matricule', key: 'matricule' },
  { title: 'Grade', key: 'grade' },
  { title: 'Unité', key: 'unite' },
  { title: 'Actions', key: 'actions', sortable: false },
]);
const etudiants = ref([]);
const editedItem = ref({});
const dialog = ref(false);
const dialogDelete = ref(false);
const formTitle = ref('');
const loading = ref(false);
const valid = ref(false);

// Options pour les menus déroulants
const sexOptions = ['Homme', 'Femme'];
const situationFamilialeOptions = ['célibataire', 'Marié'];
const niveauScolaireOptions = ['1ère', '2ème', '3ème', '4ème', '5ème'];
const gradeOptions = ['agent', 'agent régional', 'Comn', 'capi'];
const modeEngagementOptions = ['par voie directe', 'recrutement sur titre', 'direct', 'conversion'];
const anneeScolaireOptions = ['1ère année', '2ème année', '3ème année', '4ème année'];
const typeFormationOptions = ['informatique', 'musique', 'sport', 'presse'];
const natureDiplomeOptions = ['civil', 'mili'];
const specialiteOptions = ['web', 'mobile', 'sport', 'presse'];
const specialitePartielleOptions = ['Dev web mobile', 'Dev web', 'sport', 'presse'];
const formationsPrecedentesOptions = ['تكوين في إدارة', 'Dev web', 'تكوين شهادة في الرياضة'];
const diplomesPrecedentsOptions = ['diplôme Dev web mobile', 'diplôme finance', 'diplôme électricien'];
const autresDiplomesOptions = ['شهادة في الغرف', 'شهادة في الكرم'];
const cycleOptions = ['1ère session', '2ème session'];
const punitionOptions = ['15 JOUR', '30 JOUR'];
const convalescencesOptions = ['15 JOUR', '30 JOUR'];
const etatSanteOptions = ['مؤهل', 'غير مؤهل'];
const diplomeObtenuOptions = ['technicien supérieur', 'licence', 'master'];
const anneeFormationOptions = ['2022/2023', '2023/2024', '2024/2025'];

// Méthodes
const fetchEtudiants = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/etudiants');
    etudiants.value = response.data;
  } catch (error) {
    console.error('Erreur lors de la récupération des étudiants:', error);
  } finally {
    loading.value = false;
  }
};

const convertToEmployee = async (item) => {
  console.log('Objet item :', item); // Log pour déboguer

  if (!item?.id_etudiant) {
    alert('ID de l\'étudiant non trouvé.');
    return;
  }

  if (confirm('Êtes-vous sûr de vouloir convertir cet étudiant en employé ?')) {
    try {
      console.log('ID de l\'étudiant à convertir :', item.id_etudiant); // Log pour déboguer
      const response = await axios.post(`/api/etudiants/${item.id_etudiant}/convert-to-employee`);
      alert(response.data.message); // Afficher un message de succès
      fetchEtudiants(); // Rafraîchir la liste des étudiants
    } catch (error) {
      console.error('Erreur lors de la conversion:', error);
      alert('Une erreur est survenue lors de la conversion.');
    }
  }
};


const fetchGradesAndUnites = async () => {
  try {
    const response = await axios.get('/api/filters');
    grades.value = response.data.grades;
    unites.value = response.data.unites;
  } catch (error) {
    console.error('Erreur lors de la récupération des filtres:', error);
  }
};

const openDialog = (item = null) => {
  editedItem.value = item ? { ...item } : {};
  formTitle.value = item ? 'Modifier un Étudiant' : 'Ajouter un Étudiant';
  dialog.value = true;
};

const save = async () => {
  if (!valid.value) return;

  const payload = {
    ...editedItem.value,
    type_formation: JSON.stringify(editedItem.value.type_formation || []),
    specialite: JSON.stringify(editedItem.value.specialite || []),
    specialite_partielle: JSON.stringify(editedItem.value.specialite_partielle || []),
    formations_precedentes: JSON.stringify(editedItem.value.formations_precedentes || []),
    diplomes_precedents: JSON.stringify(editedItem.value.diplomes_precedents || []),
    autres_diplomes: JSON.stringify(editedItem.value.autres_diplomes || []),
    punition: JSON.stringify(editedItem.value.punition || []),
    convalescences: JSON.stringify(editedItem.value.convalescences || []),
    annee_formation: JSON.stringify(editedItem.value.annee_formation || []),
    diplome_obtenu: JSON.stringify(editedItem.value.diplome_obtenu || []),
  };

  try {
    if (editedItem.value.id) {
      await axios.put(`/api/etudiants/${editedItem.value.id}`, payload);
    } else {
      await axios.post('/api/etudiants', payload);
    }
    fetchEtudiants();
    dialog.value = false;
  } catch (error) {
    console.error('Erreur lors de la sauvegarde:', error);
  }
};

const confirmDelete = (item) => {
  editedItem.value = { ...item };
  dialogDelete.value = true;
};

const deleteItem = async () => {
  try {
    await axios.delete(`/api/etudiants/${editedItem.value.id}`);
    fetchEtudiants();
    dialogDelete.value = false;
  } catch (error) {
    console.error('Erreur lors de la suppression:', error);
  }
};

const exportData = async (type) => {
  try {
    const response = await axios.get(`/api/etudiants/export/${type}`, { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `etudiants.${type}`);
    document.body.appendChild(link);
    link.click();
  } catch (error) {
    console.error('Erreur lors de l\'exportation:', error);
  }
};

const exportSinglePDF = async (item) => {
  try {
    const response = await axios.get(`/api/etudiants/${item.id_etudiant}/export`, { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `${item.nom}_${item.prenom}.pdf`);
    document.body.appendChild(link);
    link.click();
  } catch (error) {
    console.error('Erreur lors de l\'exportation PDF:', error);
  }
};

const close = () => {
  dialog.value = false;
};

const closeDelete = () => {
  dialogDelete.value = false;
};

// Lifecycle hook
onMounted(() => {
  fetchEtudiants();
  fetchGradesAndUnites();
});
</script>

<style scoped>
.text-right {
  text-align: end;
}
</style>
