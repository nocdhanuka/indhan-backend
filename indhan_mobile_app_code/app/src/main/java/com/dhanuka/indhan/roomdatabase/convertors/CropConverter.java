package com.dhanuka.indhan.roomdatabase.convertors;

import androidx.room.TypeConverter;

import com.dhanuka.indhan.MyVillage.MyVillageBean;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;

import java.lang.reflect.Type;
import java.util.List;

public class CropConverter {
    @TypeConverter
    public static List<MyVillageBean.MyVillageData.MyVillageCrop> fromString(String value) {
        Type listType = new TypeToken<List<MyVillageBean.MyVillageData.MyVillageCrop>>() {
        }.getType();
        return new Gson().fromJson(value, listType);
    }

    @TypeConverter
    public static String fromList(List<MyVillageBean.MyVillageData.MyVillageCrop> list) {
        Gson gson = new Gson();
        String json = gson.toJson(list);
        return json;
    }
}