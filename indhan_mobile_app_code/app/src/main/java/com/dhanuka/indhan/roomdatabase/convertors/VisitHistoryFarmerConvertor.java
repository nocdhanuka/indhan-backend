package com.dhanuka.indhan.roomdatabase.convertors;

import androidx.room.TypeConverter;

import com.dhanuka.indhan.visithistory.VisitHistoryFarmerBean;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;

import java.lang.reflect.Type;
import java.util.List;

public class VisitHistoryFarmerConvertor {
    @TypeConverter
    public static List<VisitHistoryFarmerBean.Data.FarmerProduct> fromString(String value) {
        Type listType = new TypeToken<List<VisitHistoryFarmerBean.Data.FarmerProduct>>() {
        }.getType();
        return new Gson().fromJson(value, listType);
    }

    @TypeConverter
    public static String fromList(List<VisitHistoryFarmerBean.Data.FarmerProduct> list) {
        Gson gson = new Gson();
        return gson.toJson(list);
    }
}
