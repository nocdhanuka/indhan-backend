package com.dhanuka.indhan.roomdatabase.convertors;

import androidx.room.TypeConverter;

import com.dhanuka.indhan.institutional.model.sugarMill.ContactListData;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;

import java.lang.reflect.Type;
import java.util.List;

public class SugarMillContactConvertor {

    @TypeConverter
    public static List<ContactListData> fromString(String value) {
        Type listType = new TypeToken<List<ContactListData>>() {
        }.getType();
        return new Gson().fromJson(value, listType);
    }

    @TypeConverter
    public static String fromList(List<ContactListData> list) {
        Gson gson = new Gson();
        return gson.toJson(list);
    }
}
