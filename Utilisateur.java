import java.sql.Connection;

import java.sql.DriverManager;

import java.sql.SQLException;



public class DatabaseConnection {

    private static final String URL = "jdbc:mysql://localhost:3306/blog_collaboratif";

    private static final String USER = "root";

    private static final String PASSWORD = "";



    public static Connection getConnection() throws SQLException {

        try {

            Class.forName("com.mysql.cj.jdbc.Driver"); // Charger le driver MySQL

        } catch (ClassNotFoundException e) {

            System.out.println("Driver JDBC introuvable.");

        }

        return DriverManager.getConnection(URL, USER, PASSWORD);

    }

}
public class Utilisateur {

    private int id;

    private String nom;

    private String email;

    private String bio;

    private String competences;



    public Utilisateur(int id, String nom, String email, String bio, String competences) {

        this.id = id;

        this.nom = nom;

        this.email = email;

        this.bio = bio;

        this.competences = competences;

    }



    // Getters et Setters

    public int getId() {

        return id;

    }



    public void setId(int id) {

        this.id = id;

    }



    public String getNom() {

        return nom;

    }



    public void setNom(String nom) {

        this.nom = nom;

    }



    public String getEmail() {

        return email;

    }



    public void setEmail(String email) {

        this.email = email;

    }



    public String getBio() {

        return bio;

    }



    public void setBio(String bio) {

        this.bio = bio;

    }



    public String getCompetences() {

        return competences;

    }



    public void setCompetences(String competences) {

        this.competences = competences;

    }

}

import java.sql.Connection;

import java.sql.PreparedStatement;

import java.sql.ResultSet;

import java.sql.SQLException;

import java.util.ArrayList;

import java.util.List;



public class UtilisateurDAO {

    public void inscrireUtilisateur(Utilisateur utilisateur, String motDePasse) {

        String sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, bio, compétences) VALUES (?, ?, ?, ?, ?)";

        try (Connection connection = DatabaseConnection.getConnection();

             PreparedStatement statement = connection.prepareStatement(sql)) {



            statement.setString(1, utilisateur.getNom());

            statement.setString(2, utilisateur.getEmail());

            statement.setString(3, PasswordUtils.hashPassword(motDePasse)); // Hashage du mot de passe

            statement.setString(4, utilisateur.getBio());

            statement.setString(5, utilisateur.getCompetences());



            statement.executeUpdate();

        } catch (SQLException e) {

            e.printStackTrace();

        }

    }



    public Utilisateur connexionUtilisateur(String email, String motDePasse) {

        String sql = "SELECT * FROM utilisateurs WHERE email = ?";

        try (Connection connection = DatabaseConnection.getConnection();

             PreparedStatement statement = connection.prepareStatement(sql)) {



            statement.setString(1, email);

            ResultSet resultSet = statement.executeQuery();



            if (resultSet.next()) {

                String hashedPassword = resultSet.getString("mot_de_passe");

                if (PasswordUtils.verifyPassword(motDePasse, hashedPassword)) {

                    return new Utilisateur(

                        resultSet.getInt("id"),

                        resultSet.getString("nom"),

                        resultSet.getString("email"),

                        resultSet.getString("bio"),

                        resultSet.getString("compétences")

                    );

                }

            }

        } catch (SQLException e) {

            e.printStackTrace();

        }

        return null; // Retourne null si la connexion échoue

    }

}

import java.security.MessageDigest;

import java.security.NoSuchAlgorithmException;



public class PasswordUtils {

    public static String hashPassword(String password) {

        try {

            MessageDigest md = MessageDigest.getInstance("SHA-256");

            byte[] hash = md.digest(password.getBytes());

            StringBuilder hexString = new StringBuilder();

            for (byte b : hash) {

                String hex = Integer.toHexString(0xff & b);

                if (hex.length() == 1) hexString.append('0');

                hexString.append(hex);

            }

            return hexString.toString();

        } catch (NoSuchAlgorithmException e) {

            throw new RuntimeException("Erreur lors du hashage du mot de passe.", e);

        }

    }



    public static boolean verifyPassword(String password, String hashedPassword) {

        return hashPassword(password).equals(hashedPassword);

    }

}

public class Main {

    public static void main(String[] args) {

        UtilisateurDAO utilisateurDAO = new UtilisateurDAO();



        // Inscription

        Utilisateur utilisateur = new Utilisateur(0, "Jean Dupont", "jean.dupont@example.com", "Développeur passionné", "Java, MySQL");

        utilisateurDAO.inscrireUtilisateur(utilisateur, "password123");



        // Connexion

        Utilisateur utilisateurConnecte = utilisateurDAO.connexionUtilisateur("jean.dupont@example.com", "password123");

        if (utilisateurConnecte != null) {

            System.out.println("Connexion réussie pour : " + utilisateurConnecte.getNom());

        } else {

            System.out.println("Email ou mot de passe incorrect.");

        }

    }

}


