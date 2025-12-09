package com.dhanuka.indhan.roomdatabase.convertors;

import androidx.room.TypeConverter;

import com.dhanuka.indhan.institutional.model.CircularContact;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;

import java.lang.reflect.Type;
import java.util.List;

public class CircularContactConvertor {
    @TypeConverter
    public static List<CircularContact> fromString(String value) {
        Type listType = new TypeToken<List<CircularContact>>() {
        }.getType();
        return new Gson().fromJson(value, listType);
    }

    @TypeConverter
    public static String fromList(List<CircularContact> list) {
        Gson gson = new Gson();
        return gson.toJson(list);
    }
}
