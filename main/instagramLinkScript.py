from bs4 import BeautifulSoup
from urllib.parse import urljoin
import os
from selenium import webdriver
from selenium.webdriver.chrome.service import Service as ChromeService
from selenium.webdriver.support.ui import WebDriverWait
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC


URL_SITE = "https://www.instagram.com/polytech_gaming/" 
URL_RACCOURCI = "https://www.instagram.com/"

PREFIXES_A_GARDER = [
    "https://www.instagram.com/polytech_gaming/p/",
    "https://www.instagram.com/polytech_gaming/reel/"
]

SEGMENT_A_SUPPRIMER = "/polytech_gaming/" 

NOM_FICHIER_NOUVEAUX_LIENS = "nouveauLienAApprouver.txt"
NOM_FICHIER_LIENS_APPROUVES = "liens_existants.txt" 


def sauvegarder_donnees_scraping(url):
    """
    Charge la page avec Selenium et retourne la liste de tous les liens (a et link) 
    trouvés, après avoir fermé le navigateur. 
    """
    
    options = webdriver.ChromeOptions()
    options.add_argument("--headless") 
    options.add_argument("--disable-gpu")
    options.add_argument("--no-sandbox")
    
    try:
        driver = webdriver.Chrome(service=ChromeService(ChromeDriverManager().install()), options=options)
    except Exception as e:
        print("Échec de l'initialisation du WebDriver.")
        print(f"Erreur : {e}")
        return []

    liens_trouves = []
    
    try:
        print(f"\n--- 1. Scraping de la Page ---")
        print(f"Démarrage de Selenium pour charger le JavaScript de : {url}")
        driver.get(url)
        
        WebDriverWait(driver, 25).until(EC.presence_of_element_located((By.TAG_NAME, 'article')))
        print("Page chargée. Analyse du HTML...")
        
        soup = BeautifulSoup(driver.page_source, 'html.parser')

        for a_tag in soup.find_all('a', href=True):
            liens_trouves.append(urljoin(url, a_tag['href']))
            print(urljoin(url, a_tag['href']))

    except Exception as e:
        print(f"Une erreur s'est produite pendant le chargement de la page : {e}")
        return []
        
    finally:
        driver.quit()
        print("Navigateur Selenium fermé.")
    
    print(liens_trouves)
    return liens_trouves

def filtrer_liens_et_fusionner(liens_source, prefixes_filtres, segment_a_supprimer, nom_fichier_nouveaux, nom_fichier_existants):
    """
    Filtre les liens, supprime le segment non désiré, les compare à un fichier existant, 
    et ajoute les nouveaux liens.
    """
    
    print(f"\n--- 2. Filtrage et Traitement des Liens ---")
    
    liens_nettoyes_et_filtres = [] 
    liens_deja_vus = set()         
    
    for lien in liens_source:
        if any(lien.startswith(prefixe) for prefixe in prefixes_filtres):
            
            lien_nettoye = lien.replace(segment_a_supprimer, "/", 1)
            
            if lien_nettoye not in liens_deja_vus:
                liens_nettoyes_et_filtres.append(lien_nettoye) 
                liens_deja_vus.add(lien_nettoye)              

    print(f"Liens trouvés, filtrés et nettoyés ({len(liens_nettoyes_et_filtres)} uniques) tout en conservant l'ordre.")

    
    liens_existants = set()
    try:
        if os.path.exists(nom_fichier_existants):
            with open(nom_fichier_existants, 'r', encoding='utf-8') as f:
                liens_existants = {line.strip() for line in f if line.strip()}
            print(f"Chargement de {len(liens_existants)} liens existants depuis '{nom_fichier_existants}'.")
        else:
            print(f" Le fichier de liens existants '{nom_fichier_existants}' n'a pas été trouvé. Tous les liens filtrés seront considérés comme 'nouveaux'.")
    except Exception as e:
        print(f" Erreur lors du chargement des liens existants : {e}")
        

    nouveaux_liens_a_ajouter = [lien for lien in liens_nettoyes_et_filtres if lien not in liens_existants]

    if nouveaux_liens_a_ajouter:
        try:
            with open(nom_fichier_nouveaux, 'w', encoding='utf-8') as f:
                for lien in nouveaux_liens_a_ajouter:
                    f.write(lien + '\n')
            print(f"Enregistrement de {len(nouveaux_liens_a_ajouter)} NOUVEAUX liens dans '{nom_fichier_nouveaux}'.")
        except IOError as e:
            print(f"Erreur lors de l'écriture du fichier de nouveaux liens : {e}")
            
        try:
            with open(nom_fichier_existants, 'a', encoding='utf-8') as f:
                for lien in nouveaux_liens_a_ajouter:
                    f.write(lien + '\n')
            print(f"Ajout des {len(nouveaux_liens_a_ajouter)} NOUVEAUX liens à '{nom_fichier_existants}' pour la prochaine comparaison.")
        except IOError as e:
            print(f"Erreur lors de l'ajout des liens au fichier existant : {e}")
            
    else:
        print("Aucun NOUVEAU lien n'a été trouvé correspondant aux critères.")

    print("-" * 50)

tous_les_liens = sauvegarder_donnees_scraping(URL_SITE)

if tous_les_liens:
    filtrer_liens_et_fusionner(
        tous_les_liens, 
        PREFIXES_A_GARDER, 
        SEGMENT_A_SUPPRIMER, 
        NOM_FICHIER_NOUVEAUX_LIENS, 
        NOM_FICHIER_LIENS_APPROUVES
    )