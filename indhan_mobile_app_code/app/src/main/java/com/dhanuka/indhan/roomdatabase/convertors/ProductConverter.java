package com.dhanuka.indhan.roomdatabase.convertors;

import androidx.room.TypeConverter;

import com.dhanuka.indhan.farmerlist.FarmerBean;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;

import java.lang.reflect.Type;
import java.util.List;

public class ProductConverter {
    @TypeConverter
    public static List<FarmerBean.VillageFarmerData.FarmerProduct> fromString(String value) {
        Type listType = new TypeToken<List<FarmerBean.VillageFarmerData.FarmerProduct>>() {
        }.getType();
        return new Gson().fromJson(value, listType);
    }

    @TypeConverter
    public static String fromList(List<FarmerBean.VillageFarmerData.FarmerProduct> list) {
        Gson gson = new Gson();
        String json = gson.toJson(list);
        return json;
    }
}