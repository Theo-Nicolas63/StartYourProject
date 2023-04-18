var i= 0;

let maxInput = 10   ;
let x  = 1;
let donne ;
function html(i){

    // element = document.createElement()
    return '</p><div><input name="mailMembre[]"  id="nvMembreTache'+ i +'"  placeholder="Membre de la tâche"><a href="#" id="supprimer" onclick="remove()">Supprimer </a></div>';
}
function ajout() {

    i++;
    donne = html(i);
    if(x <= maxInput){
        //  document.querySelector('#container').append(donne);
        document.querySelector('#container').innerHTML +=donne;
        x++;
    }
}
function remove() {

    document.querySelector('#container').lastChild.remove();
    x--;
    i--;
    //document.querySelector('#container').remove();

}

function membre(){

    var r = new XMLHttpRequest();

    // callback
    r.onreadystatechange = function () {
        if (r.readyState === 4 && r.status === 200) {
            // alert("Success:" + r.responseText.valueOf());
            alert("dans la function membre");
            //  $("#ok").fadeIn().text("cc") ;
        }
        else {
            //alert("non");
        }
    };
    let idprojet = document.getElementById('idprojet').value;
    let membretache = new Array();
    membretache.push(document.getElementById('mailMembre').value);
    r.open("POST", "?action=AfficherPageCreationProjet" , true);
    r.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    r.send("idProjet="+idprojet + "&mailMembre[]=" + membretache);
    /* var j;
     for(j=1; j<=i; j++ ) {
         alert(j);
         membretache.push(document.getElementById('nvMembreTache' + j).value);
     }*/
}

/**
 * Fonction creer_tache() qui fait une requête
 */
function  creer_tache() {
    var r = new XMLHttpRequest();
    // callback
    r.onreadystatechange = function () {
        if (r.readyState === 4 && r.status === 200) {
            if(r.responseText == 1) {
                window.location.href = "index.php?action=AfficherPageProjet&idprojet=" + idprojet;
            }
        }
    }

    let idprojet = document.getElementById('idprojet').value;
    let nomtache = document.getElementById('nomtache').value;
    let membretache = new Array();
    membretache.push(document.getElementById('mailMembre').value);
    var j;
    for(j=1; j<=i; j++ ) {
        membretache.push(document.getElementById('nvMembreTache' + j).value);
    }
    let description = document.getElementById('description').value;
    let cout = document.getElementById('cout').value;
    let datedebut = document.getElementById('datedebut').value;
    let duree = document.getElementById('duree').value;

    r.open("POST", "?action=CreerTache" , true);
    r.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    r.send("idProjet="+idprojet +"&nomtache=" + nomtache + "&mailMembre[]=" + membretache + "&description=" + description + "&cout=" + cout +" &duree=" + duree + "&datedebut=" + datedebut);
}