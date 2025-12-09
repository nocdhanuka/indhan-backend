package com.dhanuka.indhan.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class ChemicalCompanyWiseDataList {

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
        @SerializedName("id")
        private String id;

        public String getId() {
            return id;
        }

        public void setId(String id) {
            this.id = id;
        }

        @Expose
        @SerializedName("created_at")
        private String created_at;
        @Expose
        @SerializedName("grand_total")
        private String grand_total;
        @Expose
        @SerializedName("others")
        private String others;
        @Expose
        @SerializedName("zuari_agro_chemicals")
        private String zuari_agro_chemicals;
        @Expose
        @SerializedName("willowood_crop_sciences")
        private String willowood_crop_sciences;
        @Expose
        @SerializedName("upl_ltd")
        private String upl_ltd;
        @Expose
        @SerializedName("syngenta")
        private String syngenta;
        @Expose
        @SerializedName("swal_corporation")
        private String swal_corporation;
        @Expose
        @SerializedName("sumitomo")
        private String sumitomo;
        @Expose
        @SerializedName("sulphur_mills")
        private String sulphur_mills;
        @Expose
        @SerializedName("sudarshan_chemical")
        private String sudarshan_chemical;
        @Expose
        @SerializedName("shivalik_crop")
        private String shivalik_crop;
        @Expose
        @SerializedName("safex")
        private String safex;
        @Expose
        @SerializedName("rallis")
        private String rallis;
        @Expose
        @SerializedName("punjab_chemicals")
        private String punjab_chemicals;
        @Expose
        @SerializedName("parijat")
        private String parijat;
        @Expose
        @SerializedName("pi_industries")
        private String pi_industries;
        @Expose
        @SerializedName("mahindra")
        private String mahindra;
        @Expose
        @SerializedName("multiplex")
        private String multiplex;
        @Expose
        @SerializedName("nichino")
        private String nichino;
        @Expose
        @SerializedName("nagarjuna_agri_chem")
        private String nagarjuna_agri_chem;
        @Expose
        @SerializedName("meghmani")
        private String meghmani;
        @Expose
        @SerializedName("krishi_rasayan")
        private String krishi_rasayan;
        @Expose
        @SerializedName("jubilant")
        private String jubilant;
        @Expose
        @SerializedName("ju_agri_science")
        private String ju_agri_science;
        @Expose
        @SerializedName("ipl")
        private String ipl;
        @Expose
        @SerializedName("ichiban")
        private String ichiban;
        @Expose
        @SerializedName("insecticides_india_ltd")
        private String insecticides_india_ltd;
        @Expose
        @SerializedName("indo_gulf")
        private String indo_gulf;
        @Expose
        @SerializedName("indofil")
        private String indofil;
        @Expose
        @SerializedName("heranba")
        private String heranba;
        @Expose
        @SerializedName("hpm")
        private String hpm;
        @Expose
        @SerializedName("hindustan_pulverising")
        private String hindustan_pulverising;
        @Expose
        @SerializedName("hindustan_insecticides_ltd")
        private String hindustan_insecticides_ltd;
        @Expose
        @SerializedName("gsp")
        private String gsp;
        @Expose
        @SerializedName("godrej_agrovet_ltd")
        private String godrej_agrovet_ltd;
        @Expose
        @SerializedName("gharda_chemicals")
        private String gharda_chemicals;
        @Expose
        @SerializedName("fmc")
        private String fmc;
        @Expose
        @SerializedName("fil")
        private String fil;
        @Expose
        @SerializedName("excel_crop_care")
        private String excel_crop_care;
        @Expose
        @SerializedName("dhanuka_agritech")
        private String dhanuka_agritech;
        @Expose
        @SerializedName("devidayal")
        private String devidayal;
        @Expose
        @SerializedName("corteva")
        private String corteva;
        @Expose
        @SerializedName("crystal")
        private String crystal;
        @Expose
        @SerializedName("coromandel_agri_co")
        private String coromandel_agri_co;
        @Expose
        @SerializedName("coromandal_inter_ltd")
        private String coromandal_inter_ltd;
        @Expose
        @SerializedName("cheminova")
        private String cheminova;
        @Expose
        @SerializedName("bio_stadt")
        private String bio_stadt;
        @Expose
        @SerializedName("bharat_certis")
        private String bharat_certis;
        @Expose
        @SerializedName("basf")
        private String basf;
        @Expose
        @SerializedName("bayer")
        private String bayer;
        @Expose
        @SerializedName("atul")
        private String atul;
        @Expose
        @SerializedName("adama")
        private String adama;
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
        @SerializedName("status")
        private String status;

        public String getStatus() {
            return status;
        }

        public void setStatus(String status) {
            this.status = status;
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

        public String getGrand_total() {
            return grand_total;
        }

        public void setGrand_total(String grand_total) {
            this.grand_total = grand_total;
        }

        public String getOthers() {
            return others;
        }

        public void setOthers(String others) {
            this.others = others;
        }

        public String getZuari_agro_chemicals() {
            return zuari_agro_chemicals;
        }

        public void setZuari_agro_chemicals(String zuari_agro_chemicals) {
            this.zuari_agro_chemicals = zuari_agro_chemicals;
        }

        public String getWillowood_crop_sciences() {
            return willowood_crop_sciences;
        }

        public void setWillowood_crop_sciences(String willowood_crop_sciences) {
            this.willowood_crop_sciences = willowood_crop_sciences;
        }

        public String getUpl_ltd() {
            return upl_ltd;
        }

        public void setUpl_ltd(String upl_ltd) {
            this.upl_ltd = upl_ltd;
        }

        public String getSyngenta() {
            return syngenta;
        }

        public void setSyngenta(String syngenta) {
            this.syngenta = syngenta;
        }

        public String getSwal_corporation() {
            return swal_corporation;
        }

        public void setSwal_corporation(String swal_corporation) {
            this.swal_corporation = swal_corporation;
        }

        public String getSumitomo() {
            return sumitomo;
        }

        public void setSumitomo(String sumitomo) {
            this.sumitomo = sumitomo;
        }

        public String getSulphur_mills() {
            return sulphur_mills;
        }

        public void setSulphur_mills(String sulphur_mills) {
            this.sulphur_mills = sulphur_mills;
        }

        public String getSudarshan_chemical() {
            return sudarshan_chemical;
        }

        public void setSudarshan_chemical(String sudarshan_chemical) {
            this.sudarshan_chemical = sudarshan_chemical;
        }

        public String getShivalik_crop() {
            return shivalik_crop;
        }

        public void setShivalik_crop(String shivalik_crop) {
            this.shivalik_crop = shivalik_crop;
        }

        public String getSafex() {
            return safex;
        }

        public void setSafex(String safex) {
            this.safex = safex;
        }

        public String getRallis() {
            return rallis;
        }

        public void setRallis(String rallis) {
            this.rallis = rallis;
        }

        public String getPunjab_chemicals() {
            return punjab_chemicals;
        }

        public void setPunjab_chemicals(String punjab_chemicals) {
            this.punjab_chemicals = punjab_chemicals;
        }

        public String getParijat() {
            return parijat;
        }

        public void setParijat(String parijat) {
            this.parijat = parijat;
        }

        public String getPi_industries() {
            return pi_industries;
        }

        public void setPi_industries(String pi_industries) {
            this.pi_industries = pi_industries;
        }

        public String getMahindra() {
            return mahindra;
        }

        public void setMahindra(String mahindra) {
            this.mahindra = mahindra;
        }

        public String getMultiplex() {
            return multiplex;
        }

        public void setMultiplex(String multiplex) {
            this.multiplex = multiplex;
        }

        public String getNichino() {
            return nichino;
        }

        public void setNichino(String nichino) {
            this.nichino = nichino;
        }

        public String getNagarjuna_agri_chem() {
            return nagarjuna_agri_chem;
        }

        public void setNagarjuna_agri_chem(String nagarjuna_agri_chem) {
            this.nagarjuna_agri_chem = nagarjuna_agri_chem;
        }

        public String getMeghmani() {
            return meghmani;
        }

        public void setMeghmani(String meghmani) {
            this.meghmani = meghmani;
        }

        public String getKrishi_rasayan() {
            return krishi_rasayan;
        }

        public void setKrishi_rasayan(String krishi_rasayan) {
            this.krishi_rasayan = krishi_rasayan;
        }

        public String getJubilant() {
            return jubilant;
        }

        public void setJubilant(String jubilant) {
            this.jubilant = jubilant;
        }

        public String getJu_agri_science() {
            return ju_agri_science;
        }

        public void setJu_agri_science(String ju_agri_science) {
            this.ju_agri_science = ju_agri_science;
        }

        public String getIpl() {
            return ipl;
        }

        public void setIpl(String ipl) {
            this.ipl = ipl;
        }

        public String getIchiban() {
            return ichiban;
        }

        public void setIchiban(String ichiban) {
            this.ichiban = ichiban;
        }

        public String getInsecticides_india_ltd() {
            return insecticides_india_ltd;
        }

        public void setInsecticides_india_ltd(String insecticides_india_ltd) {
            this.insecticides_india_ltd = insecticides_india_ltd;
        }

        public String getIndo_gulf() {
            return indo_gulf;
        }

        public void setIndo_gulf(String indo_gulf) {
            this.indo_gulf = indo_gulf;
        }

        public String getIndofil() {
            return indofil;
        }

        public void setIndofil(String indofil) {
            this.indofil = indofil;
        }

        public String getHeranba() {
            return heranba;
        }

        public void setHeranba(String heranba) {
            this.heranba = heranba;
        }

        public String getHpm() {
            return hpm;
        }

        public void setHpm(String hpm) {
            this.hpm = hpm;
        }

        public String getHindustan_pulverising() {
            return hindustan_pulverising;
        }

        public void setHindustan_pulverising(String hindustan_pulverising) {
            this.hindustan_pulverising = hindustan_pulverising;
        }

        public String getHindustan_insecticides_ltd() {
            return hindustan_insecticides_ltd;
        }

        public void setHindustan_insecticides_ltd(String hindustan_insecticides_ltd) {
            this.hindustan_insecticides_ltd = hindustan_insecticides_ltd;
        }

        public String getGsp() {
            return gsp;
        }

        public void setGsp(String gsp) {
            this.gsp = gsp;
        }

        public String getGodrej_agrovet_ltd() {
            return godrej_agrovet_ltd;
        }

        public void setGodrej_agrovet_ltd(String godrej_agrovet_ltd) {
            this.godrej_agrovet_ltd = godrej_agrovet_ltd;
        }

        public String getGharda_chemicals() {
            return gharda_chemicals;
        }

        public void setGharda_chemicals(String gharda_chemicals) {
            this.gharda_chemicals = gharda_chemicals;
        }

        public String getFmc() {
            return fmc;
        }

        public void setFmc(String fmc) {
            this.fmc = fmc;
        }

        public String getFil() {
            return fil;
        }

        public void setFil(String fil) {
            this.fil = fil;
        }

        public String getExcel_crop_care() {
            return excel_crop_care;
        }

        public void setExcel_crop_care(String excel_crop_care) {
            this.excel_crop_care = excel_crop_care;
        }

        public String getDhanuka_agritech() {
            return dhanuka_agritech;
        }

        public void setDhanuka_agritech(String dhanuka_agritech) {
            this.dhanuka_agritech = dhanuka_agritech;
        }

        public String getDevidayal() {
            return devidayal;
        }

        public void setDevidayal(String devidayal) {
            this.devidayal = devidayal;
        }

        public String getCorteva() {
            return corteva;
        }

        public void setCorteva(String corteva) {
            this.corteva = corteva;
        }

        public String getCrystal() {
            return crystal;
        }

        public void setCrystal(String crystal) {
            this.crystal = crystal;
        }

        public String getCoromandel_agri_co() {
            return coromandel_agri_co;
        }

        public void setCoromandel_agri_co(String coromandel_agri_co) {
            this.coromandel_agri_co = coromandel_agri_co;
        }

        public String getCoromandal_inter_ltd() {
            return coromandal_inter_ltd;
        }

        public void setCoromandal_inter_ltd(String coromandal_inter_ltd) {
            this.coromandal_inter_ltd = coromandal_inter_ltd;
        }

        public String getCheminova() {
            return cheminova;
        }

        public void setCheminova(String cheminova) {
            this.cheminova = cheminova;
        }

        public String getBio_stadt() {
            return bio_stadt;
        }

        public void setBio_stadt(String bio_stadt) {
            this.bio_stadt = bio_stadt;
        }

        public String getBharat_certis() {
            return bharat_certis;
        }

        public void setBharat_certis(String bharat_certis) {
            this.bharat_certis = bharat_certis;
        }

        public String getBasf() {
            return basf;
        }

        public void setBasf(String basf) {
            this.basf = basf;
        }

        public String getBayer() {
            return bayer;
        }

        public void setBayer(String bayer) {
            this.bayer = bayer;
        }

        public String getAtul() {
            return atul;
        }

        public void setAtul(String atul) {
            this.atul = atul;
        }

        public String getAdama() {
            return adama;
        }

        public void setAdama(String adama) {
            this.adama = adama;
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
