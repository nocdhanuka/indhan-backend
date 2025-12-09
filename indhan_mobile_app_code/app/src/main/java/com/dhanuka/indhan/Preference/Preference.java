package com.dhanuka.indhan.Preference;

import android.content.Context;
import android.content.SharedPreferences;

import com.dhanuka.indhan.util.SelectedNumberModel;
import com.google.gson.Gson;

public class Preference {
    private static final String PREF_SELECTEDNUM = "selectednum";
    private static Preference preference;
    private SharedPreferences sharedPreferences;

    public Preference(Context context) {
        sharedPreferences = context.getSharedPreferences("InDhanPref", Context.MODE_PRIVATE);
    }

    public static Preference getInstance(Context context) {
        if (preference == null) {
            preference = new Preference(context);
        }
        return preference;
    }

    public static Preference getInstance() {
        return preference;
    }

    public static void addPreferenceSelected(Context context,
                                             String key,
                                             SelectedNumberModel model) {

        SharedPreferences.Editor editor = context.getSharedPreferences(PREF_SELECTEDNUM, Context.MODE_PRIVATE).edit();
        Gson gson = new Gson();
        String json = gson.toJson(model);
        editor.putString(key, json);
        editor.apply();

    }

    public static SelectedNumberModel getPreferenceSelected(Context context, String key) {

        SharedPreferences prefs = context.getSharedPreferences(PREF_SELECTEDNUM, Context.MODE_PRIVATE);
        Gson gson = new Gson();
        String json = prefs.getString(key, "");
        SelectedNumberModel model = gson.fromJson(json, SelectedNumberModel.class);
        return model;
    }

    public static void destroyInstance() {
        if (preference != null) {
            preference = null;
        }
    }

    public void putBolean(String key, boolean value) {
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putBoolean(key, value);
        editor.commit();
    }

    public void putString(String key, String value) {
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putString(key, value);
        editor.commit();
    }

    /*public static void addPreferenceSelected(Context context,
                                             String key,
                                             ArrayList<LogObject> logObjects) {
        SharedPreferences.Editor editor = context.getSharedPreferences
                (PREF_SELECTEDNUM, Context.MODE_PRIVATE).edit();
        Gson gson = new Gson();
        String json = gson.toJson(logObjects);
        editor.putString(key, json);
        editor.apply();
    }*/

    public void putInt(String key, int value) {
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putInt(key, value);
        editor.commit();
    }

    /*public static ArrayList<LogObject> getPreferenceSelected(Context context, String key) {
        SharedPreferences prefs = context.
                getSharedPreferences(PREF_SELECTEDNUM, Context.MODE_PRIVATE);
        Gson gson = new Gson();

        String json = prefs.getString(key, "");

        SelectedNumbers selectedNumbers = gson.fromJson(json, SelectedNumbers.class);

        List logObjects = Arrays.asList(selectedNumbers);
        logObjects = new ArrayList(logObjects);
        if (logObjects != null)
            return (ArrayList) logObjects;
        else
            return new ArrayList<LogObject>();

    }*/

    public void putLong(String key, Long value) {
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putLong(key, value);
        editor.commit();
    }






       /* SharedPreferences prefs = context.
                getSharedPreferences(PREF_SELECTEDNUM, Context.MODE_PRIVATE);
        Gson gson = new Gson();

        String json = prefs.getString(key, "");

        SelectedNumbers selectedNumbers = gson.fromJson(json, SelectedNumbers.class);

        List logObjects = Arrays.asList(selectedNumbers);
        logObjects = new ArrayList(logObjects);
        if (logObjects != null)
            return (ArrayList) logObjects;
        else
            return new ArrayList<String>();*/

    public boolean getBoolean(String key) {
        return sharedPreferences.getBoolean(key, Boolean.FALSE);
    }

    public String getString(String key) {
        return sharedPreferences.getString(key, "");
    }

    public String getTokenString(String key) {
        return sharedPreferences.getString(key, "0");
    }

    public int getInt(String key) {
        return sharedPreferences.getInt(key, 0);
    }

    public long getLong(String key) {
        return sharedPreferences.getLong(key, 0);
    }

    public void clearAllData(){
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.clear();
        editor.apply();
    }
}
