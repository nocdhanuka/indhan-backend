package com.dhanuka.indhan.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class ChemicalCropWiseListResponse {

    @Expose
    @SerializedName("data")
    private List<Data> data;
    @Expose
    @SerializedName("message")
    private String message;
    @Expose
    @SerializedName("status_code")
    private int status_code;
    @Expose
    @SerializedName("status")
    private String status;

    public List<Data> getData() {
        return data;
    }

    public void setData(List<Data> data) {
        this.data = data;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public int getStatus_code() {
        return status_code;
    }

    public void setStatus_code(int status_code) {
        this.status_code = status_code;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public static class Data {
        @Expose
        @SerializedName("updated_at")
        private String updated_at;
        @Expose
        @SerializedName("status")
        private String status;

        public String getStatus() {
            return status;
        }

        public void setStatus(String status) {
            this.status = status;
        }

        @Expose
        @SerializedName("created_at")
        private String created_at;
        @Expose
        @SerializedName("percentage")
        private String percentage;
        @Expose
        @SerializedName("total")
        private String total;
        @Expose
        @SerializedName("others")
        private String others;
        @Expose
        @SerializedName("other_veg")
        private String other_veg;
        @Expose
        @SerializedName("tomato")
        private String tomato;
        @Expose
        @SerializedName("potato")
        private String potato;
        @Expose
        @SerializedName("onion_garlic")
        private String onion_garlic;
        @Expose
        @SerializedName("cucurbits_melons")
        private String cucurbits_melons;
        @Expose
        @SerializedName("cole_crops")
        private String cole_crops;
        @Expose
        @SerializedName("chillies")
        private String chillies;
        @Expose
        @SerializedName("brinjal")
        private String brinjal;
        @Expose
        @SerializedName("cumin")
        private String cumin;
        @Expose
        @SerializedName("other_pulses")
        private String other_pulses;
        @Expose
        @SerializedName("black_gram")
        private String black_gram;
        @Expose
        @SerializedName("bengal_gram")
        private String bengal_gram;
        @Expose
        @SerializedName("tea")
        private String tea;
        @Expose
        @SerializedName("sugarcane")
        private String sugarcane;
        @Expose
        @SerializedName("other_oilseeds")
        private String other_oilseeds;
        @Expose
        @SerializedName("soybean")
        private String soybean;
        @Expose
        @SerializedName("groundnut")
        private String groundnut;
        @Expose
        @SerializedName("other_fruits")
        private String other_fruits;
        @Expose
        @SerializedName("citrus")
        private String citrus;
        @Expose
        @SerializedName("pomegranate")
        private String pomegranate;
        @Expose
        @SerializedName("mango")
        private String mango;
        @Expose
        @SerializedName("apple")
        private String apple;
        @Expose
        @SerializedName("grapes")
        private String grapes;
        @Expose
        @SerializedName("cotton")
        private String cotton;
        @Expose
        @SerializedName("wheat")
        private String wheat;
        @Expose
        @SerializedName("rice")
        private String rice;
        @Expose
        @SerializedName("maize")
        private String maize;
        @Expose
        @SerializedName("value")
        private String value;
        @Expose
        @SerializedName("volume")
        private String volume;
        @Expose
        @SerializedName("formulationname")
        private String formulationname;
        @Expose
        @SerializedName("classname")
        private String classname;
        @Expose
        @SerializedName("district")
        private String district;
        @Expose
        @SerializedName("state")
        private String state;
        @Expose
        @SerializedName("ecnname")
        private String ecnname;
        @Expose
        @SerializedName("ecnnumber")
        private String ecnnumber;
        @Expose
        @SerializedName("id")
        public String id;

        public String getId() {
            return id;
        }

        public void setId(String id) {
            this.id = id;
        }

        public String getUpdated_at() {
            return updated_at;
        }

        public void setUpdated_at(String updated_at) {
            this.updated_at = updated_at;
        }

        public String getCreated_at() {
            return created_at;
        }

        public void setCreated_at(String created_at) {
            this.created_at = created_at;
        }

        public String getPercentage() {
            return percentage;
        }

        public void setPercentage(String percentage) {
            this.percentage = percentage;
        }

        public String getTotal() {
            return total;
        }

        public void setTotal(String total) {
            this.total = total;
        }

        public String getOthers() {
            return others;
        }

        public void setOthers(String others) {
            this.others = others;
        }

        public String getOther_veg() {
            return other_veg;
        }

        public void setOther_veg(String other_veg) {
            this.other_veg = other_veg;
        }

        public String getTomato() {
            return tomato;
        }

        public void setTomato(String tomato) {
            this.tomato = tomato;
        }

        public String getPotato() {
            return potato;
        }

        public void setPotato(String potato) {
            this.potato = potato;
        }

        public String getOnion_garlic() {
            return onion_garlic;
        }

        public void setOnion_garlic(String onion_garlic) {
            this.onion_garlic = onion_garlic;
        }

        public String getCucurbits_melons() {
            return cucurbits_melons;
        }

        public void setCucurbits_melons(String cucurbits_melons) {
            this.cucurbits_melons = cucurbits_melons;
        }

        public String getCole_crops() {
            return cole_crops;
        }

        public void setCole_crops(String cole_crops) {
            this.cole_crops = cole_crops;
        }

        public String getChillies() {
            return chillies;
        }

        public void setChillies(String chillies) {
            this.chillies = chillies;
        }

        public String getBrinjal() {
            return brinjal;
        }

        public void setBrinjal(String brinjal) {
            this.brinjal = brinjal;
        }

        public String getCumin() {
            return cumin;
        }

        public void setCumin(String cumin) {
            this.cumin = cumin;
        }

        public String getOther_pulses() {
            return other_pulses;
        }

        public void setOther_pulses(String other_pulses) {
            this.other_pulses = other_pulses;
        }

        public String getBlack_gram() {
            return black_gram;
        }

        public void setBlack_gram(String black_gram) {
            this.black_gram = black_gram;
        }

        public String getBengal_gram() {
            return bengal_gram;
        }

        public void setBengal_gram(String bengal_gram) {
            this.bengal_gram = bengal_gram;
        }

        public String getTea() {
            return tea;
        }

        public void setTea(String tea) {
            this.tea = tea;
        }

        public String getSugarcane() {
            return sugarcane;
        }

        public void setSugarcane(String sugarcane) {
            this.sugarcane = sugarcane;
        }

        public String getOther_oilseeds() {
            return other_oilseeds;
        }

        public void setOther_oilseeds(String other_oilseeds) {
            this.other_oilseeds = other_oilseeds;
        }

        public String getSoybean() {
            return soybean;
        }

        public void setSoybean(String soybean) {
            this.soybean = soybean;
        }

        public String getGroundnut() {
            return groundnut;
        }

        public void setGroundnut(String groundnut) {
            this.groundnut = groundnut;
        }

        public String getOther_fruits() {
            return other_fruits;
        }

        public void setOther_fruits(String other_fruits) {
            this.other_fruits = other_fruits;
        }

        public String getCitrus() {
            return citrus;
        }

        public void setCitrus(String citrus) {
            this.citrus = citrus;
        }

        public String getPomegranate() {
            return pomegranate;
        }

        public void setPomegranate(String pomegranate) {
            this.pomegranate = pomegranate;
        }

        public String getMango() {
            return mango;
        }

        public void setMango(String mango) {
            this.mango = mango;
        }

        public String getApple() {
            return apple;
        }

        public void setApple(String apple) {
            this.apple = apple;
        }

        public String getGrapes() {
            return grapes;
        }

        public void setGrapes(String grapes) {
            this.grapes = grapes;
        }

        public String getCotton() {
            return cotton;
        }

        public void setCotton(String cotton) {
            this.cotton = cotton;
        }

        public String getWheat() {
            return wheat;
        }

        public void setWheat(String wheat) {
            this.wheat = wheat;
        }

        public String getRice() {
            return rice;
        }

        public void setRice(String rice) {
            this.rice = rice;
        }

        public String getMaize() {
            return maize;
        }

        public void setMaize(String maize) {
            this.maize = maize;
        }

        public String getValue() {
            return value;
        }

        public void setValue(String value) {
            this.value = value;
        }

        public String getVolume() {
            return volume;
        }

        public void setVolume(String volume) {
            this.volume = volume;
        }

        public String getFormulationname() {
            return formulationname;
        }

        public void setFormulationname(String formulationname) {
            this.formulationname = formulationname;
        }

        public String getClassname() {
            return classname;
        }

        public void setClassname(String classname) {
            this.classname = classname;
        }

        public String getDistrict() {
            return district;
        }

        public void setDistrict(String district) {
            this.district = district;
        }

        public String getState() {
            return state;
        }

        public void setState(String state) {
            this.state = state;
        }

        public String getEcnname() {
            return ecnname;
        }

        public void setEcnname(String ecnname) {
            this.ecnname = ecnname;
        }

        public String getEcnnumber() {
            return ecnnumber;
        }

        public void setEcnnumber(String ecnnumber) {
            this.ecnnumber = ecnnumber;
        }
    }
}
