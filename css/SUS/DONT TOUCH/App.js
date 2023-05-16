import { StyleSheet,TouchableOpacity, Text, View, Button , FlatList, TextInput} from 'react-native';
import { createContext,useContext,useState } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import {createBottomTabNavigator} from '@react-navigation/bottom-tabs';
import MapView from 'react-native-maps';
import Ionicons from '@expo/vector-icons/Ionicons';
import voyages from "./trips";
import {Database} from "./database";

const UserContext = createContext('Test'); //1-context (défaut)

const db = new Database('shopJOMO.db');// returns Database object
global.db = db;

const createTable = () => {
  executeDB(
 "CREATE TABLE IF NOT EXISTS " +
 "Connexion(id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT UNIQUE, mdp TEXT, admin BOOLEAN); "+

 "CREATE TABLE IF NOT EXISTS "+ 
 "Produits(id INTEGER PRIMARY KEY AUTOINCREMENT, nom TEXT UNIQUE, description TEXT, prix FLOAT, image TEXT);"
);

db.execute(`INSERT INTO Produits (nom, description,prix,image) VALUES('Galaxy S21', 'Samsung telephone haut de gamme',200,"https://i5.walmartimages.com/asr/66acdf90-d921-4511-8cd8-30f23b699ab4.3ef26080af3de4a117fca0eb0a42edd0.jpeg?odnHeight=450&odnWidth=450&odnBg=ffffff");`);
db.execute(`INSERT INTO Produits (nom, description,prix,image) VALUES('iPhone 13 PRO', 'Telephone Apple Iphone 13 PRO très bon mais la batterie est de la marde',899,"https://gocdkeys.com/images/captures/iphone-13-pro-max-smartphone-1.jpg");`);
db.execute(`INSERT INTO Produits (nom, description,prix,image) VALUES('Macbook Air', 'Un ordinateur portable assez puissant et très leger',3199,"https://th.bing.com/th/id/R.e87cff0dbddb57c93f5d48df87a1b6db?rik=YTSkq81ZkP914w&pid=ImgRaw&r=0");`);
db.execute(`INSERT INTO Produits (nom, description,prix,image) VALUES('iPad PRO', 'Un iPad pour surstimuler encore plus votre adorable enfant',699,"https://th.bing.com/th/id/R.566b3cccbf62b91e0ccbfabe63dd806e?rik=dYWU8rsMxJnGfg&pid=ImgRaw&r=0");`);
db.execute("INSERT INTO Connexion(username,mdp,admin) VALUES('test','test',0);");
db.execute("INSERT INTO Connexion(username,mdp,admin) VALUES('wow','wow',1);");
//db.execute(`SELECT * from Produits`).then(resultSet => setQqchose(resultSet.rows));
}
function executeDB(stringSql){
  return db.execute(stringSql);
}
function essayerLogin(id,pwd){

  //console.log(global.inpIdentifiant);


  //console.log("aaa");
  //console.log(db);
  
  db.execute(`SELECT * FROM Connexion WHERE username="`+id+`" AND mdp="`+pwd+`";`).then(resultSet => {
    console.log(resultSet);
    global.setConnectedId(-1);
    if(resultSet.rows.length == 1){global.setConnectedId(resultSet.rows[0].id); console.log(global.connectedId); console.log("connecté en tant que "+global.connectedId);}
    else{console.log("AUCUN COMPTE");}
  });
  //global.db.execute("SELECT * FROM connexion WHERE username="+global.inpIdentifiant);
}

const ParametresProduit = (props) =>
{
  return(<View>
            <Text style={styles.detailsCroisières}>Destination : {props.Destination} | port de départ : {props.Départ}</Text>
         </View>);
}
const Produits = (props) =>
{
  return(
    <View>
      <Text style={styles.title}>{props.CruiseLine}</Text>
      <FlatList
        data={props.Croisières}
        renderItem={({item})=> <ParametresProduit Destination={item.destination} Départ={item.depart}/>}
      />
    </View>
  );
}

  const PAcceuil = ({navigation}) => { 


    var inpIdentifiant = "";
    var inpPassword = "";

    
    //setConnectedId(3);

    console.log("TEST LOGIN =>" + connectedId);

    return(<View style={[styles.container, styles.center,{height:600}]}>
      <Text style={styles.center} >Entrez vos informations</Text>
      <TextInput onChangeText={x => {inpIdentifiant = x}} style={styles.InputStyle} placeholder='Identifiant'></TextInput>
      <TextInput onChangeText={x => {inpPassword = x}}style={styles.InputStyle} placeholder='Mot de passe'></TextInput>
      <Button title="Connexion" onPress={() => {essayerLogin(inpIdentifiant,inpPassword)}}></Button>
    </View>
    );
    }
  
  const PMagasin = ({navigation}) => {
    return (
    <View style={styles.container}>
      <Text style={styles.title}>Nos croisières en promotions</Text>
      <FlatList 
        data={voyages} 
        renderItem={({item})=> <Produits CruiseLine={item.cruiseLine} Croisières={item.croisieres}/>}
      />
    </View>
    );
    }

  const PPanier = ({navigation}) => {
    return (
    <View style={styles.container}>
      <Text>Page de Recherche</Text>
    </View>
    );
    }
    
  const PMap = ({navigation}) => {
    return (
    <View style={styles.container}>
      <Text>Page de croisière</Text>
    </View>
    );
    }


  const PAdmin = ({navigation}) => {
    return (
    <View style={styles.container}>
      <Text>Page de croisière</Text>
    </View>
    );
    }

  //C'est l'endroit ou on achète l'item
  const PDétails = ({navigation}) => {
    return (
    <View style={styles.container}>
      <Text>Page de croisière</Text>
    </View>
    );
    }
      
  const PÀPropos = ({navigation}) => {
    return (
    <View style={styles.container}>
      <Text>Page de croisière</Text>
    </View>
    );
    }
    
const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

export default function App() {
  const [connectedId,setConnectedId] = useState();
  global.connectedId = connectedId;
  global.setConnectedId = setConnectedId;
  createTable();
  return (
    <UserContext.Provider value={{connectedId, setConnectedId}  /**** */}>
      <NavigationContainer initialRouteName="Shop">
        <Tab.Navigator>

          <Tab.Screen name="Acceuil" component={PAcceuil}
            options={{tabBarIcon: ({focused}) => <Ionicons name="map"
            color={focused ? "#483d8b" : "gray"} size={35}/>}} /> 

          <Tab.Screen name="Magasin" component={PMagasin}
            options={{tabBarIcon: ({focused}) => <Ionicons name="map"
            color={focused ? "#483d8b" : "gray"} size={35}/>}} /> 

          <Tab.Screen name="Map" component={PMap} 
            options={{tabBarIcon: ({focused}) => <Ionicons name="map"
            color={focused ? "#483d8b" : "gray"} size={35}/>}}/>

          <Tab.Screen name="Panier" component={PPanier} 
            options={{tabBarIcon: ({focused}) => <Ionicons name="map"
            color={focused ? "#483d8b" : "gray"} size={35}/>}}/>

          <Tab.Screen name="Admin" component={PAdmin} 
            options={{tabBarIcon: ({focused}) => <Ionicons name="map"
            color={focused ? "#483d8b" : "gray"} size={35}/>}}/>

          <Tab.Screen name="Détails" component={PDétails} 
            options={{tabBarIcon: ({focused}) => <Ionicons name="map"
            color={focused ? "#483d8b" : "gray"} size={35}/>}}/>

          <Tab.Screen name="À propos" component={PÀPropos} 
            options={{tabBarIcon: ({focused}) => <Ionicons name="ios-boat-sharp"
            color={focused ? "#483d8b" : "gray"} size={35}/>}}/>

        </Tab.Navigator>
      </NavigationContainer>
    </UserContext.Provider>
  );
}

const styles = StyleSheet.create({
  center: {
    justifyContent: 'center',
    top:20,
  },
  InputStyle:{
    backgroundColor: 'gray',
    margin:5,
    height:50,
    color:'#483d8b',
    fontSize:20
  },
  container: {
    backgroundColor: 'black',
  },
  title: {
    backgroundColor: '#483d8b',
    color: "white",
    fontSize:35,
    margin:10,
  },
  detailsCroisières: {
    fontSize: 15,
    fontWeight:"bold",
    padding: 4,
    margin : 4,
    color:"white"
  },
});
