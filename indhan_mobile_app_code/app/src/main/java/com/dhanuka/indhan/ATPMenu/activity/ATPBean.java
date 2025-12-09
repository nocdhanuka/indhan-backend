package com.dhanuka.indhan.ATPMenu.activity;

import androidx.room.Entity;
import androidx.room.Ignore;
import androidx.room.PrimaryKey;

import com.dhanuka.indhan.ATPMenu.activity.models.ValidatedfarmerListResponse;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.List;

public class ATPBean {
    private ArrayList<Data> data;

    private String message;

    private String status;

    private Integer status_code;

    public ArrayList<Data> getData() {
        return this.data;
    }

    public void setData(ArrayList<Data> data) {
        this.data = data;
    }

    public String getMessage() {
        return this.message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public String getStatus() {
        return this.status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public Integer getStatus_code() {
        return this.status_code;
    }

    public void setStatus_code(Integer status_code) {
        this.status_code = status_code;
    }

    @Entity
    public static class Data implements Serializable {

        @PrimaryKey(autoGenerate = true)
        public int id;
        private String created_at;

        private String created_by_user_id;

        @Ignore
        private ArrayList<Crop> crop;

        private String crop_id;

        private String data_source_id;

        private String dd_id;

        private String demo_request_id;

        private String district;

        private String district_id;

        private String territory_id;

        private String territory;

        private String expected_farmer;

        private String expected_pda;

        private String image;

        private String is_deleted;

        private String language_id;

        private String other_crop_1;

        private String other_crop_2;

        private String plan_atp_id;

        private String plan_date;

        @Ignore
        private List<Product> product;

        private String product_id;

        private String reason;

        private String record_id;

        private String state;

        private String state_id;

        private String status;

        private String tehsil;

        private String tehsil_id;

        private String updated_at;

        private String user_type;

        private String village;

        @Ignore
        private String presenter_id;

        @Ignore
        private String presenter_type;

        @Ignore
        private String monitor_id;

        @Ignore
        private String monitor_type;

        @Ignore
        private String meeting_date;

        @Ignore
        private String meeting_detail;

        @Ignore
        private String meeting_status;

        public String getTerritory_id() {
            return territory_id;
        }

        public void setTerritory_id(String territory_id) {
            this.territory_id = territory_id;
        }

        public String getTerritory() {
            return territory;
        }

        public void setTerritory(String territory) {
            this.territory = territory;
        }

        public String getPresenter_id() {
            return presenter_id;
        }

        public void setPresenter_id(String presenter_id) {
            this.presenter_id = presenter_id;
        }

        public String getPresenter_type() {
            return presenter_type;
        }

        public void setPresenter_type(String presenter_type) {
            this.presenter_type = presenter_type;
        }

        public String getMonitor_id() {
            return monitor_id;
        }

        public void setMonitor_id(String monitor_id) {
            this.monitor_id = monitor_id;
        }

        public String getMonitor_type() {
            return monitor_type;
        }

        public void setMonitor_type(String monitor_type) {
            this.monitor_type = monitor_type;
        }

        public String getMeeting_date() {
            return meeting_date;
        }

        public void setMeeting_date(String meeting_date) {
            this.meeting_date = meeting_date;
        }

        public String getMeeting_detail() {
            return meeting_detail;
        }

        public void setMeeting_detail(String meeting_detail) {
            this.meeting_detail = meeting_detail;
        }

        public String getMeeting_status() {
            return meeting_status;
        }

        public void setMeeting_status(String meeting_status) {
            this.meeting_status = meeting_status;
        }

        private String village_id;

        private String ticket_id;

        private String ticket_no;

        private String total_farmers;

        private String total_invited_farmers;

        private String dd_name;

        private String forwhom;
        @Ignore
        private String bdo_name;
        @Ignore
        private Object latitude;
        @Ignore
        private Object longitude;
        private boolean isTicketClosed;
        private boolean isTicketActivated;
        @Ignore
        private ArrayList<ValidatedfarmerListResponse.Data> invitedFarmerlist;

        public String getBdo_name() {
            return bdo_name;
        }

        public void setBdo_name(String bdo_name) {
            this.bdo_name = bdo_name;
        }

        public boolean isTicketClosed() {
            return isTicketClosed;
        }

        public void setTicketClosed(boolean ticketClosed) {
            isTicketClosed = ticketClosed;
        }

        public boolean isTicketActivated() {
            return isTicketActivated;
        }

        public void setTicketActivated(boolean ticketActivated) {
            isTicketActivated = ticketActivated;
        }

        public ArrayList<ValidatedfarmerListResponse.Data> getInvitedFarmerlist() {
            return invitedFarmerlist;
        }

        public void setInvitedFarmerlist(ArrayList<ValidatedfarmerListResponse.Data> invitedFarmerlist) {
            this.invitedFarmerlist = invitedFarmerlist;
        }


        public String getCreated_at() {
            return this.created_at;
        }

        public void setCreated_at(String created_at) {
            this.created_at = created_at;
        }

        public String getCreated_by_user_id() {
            return this.created_by_user_id;
        }

        public void setCreated_by_user_id(String created_by_user_id) {
            this.created_by_user_id = created_by_user_id;
        }

        public ArrayList<Crop> getCrop() {
            return this.crop;
        }

        public void setCrop(ArrayList<Crop> crop) {
            this.crop = crop;
        }

        public String getCrop_id() {
            return this.crop_id;
        }

        public void setCrop_id(String crop_id) {
            this.crop_id = crop_id;
        }

        public String getData_source_id() {
            return this.data_source_id;
        }

        public void setData_source_id(String data_source_id) {
            this.data_source_id = data_source_id;
        }

        public String getDd_id() {
            return this.dd_id;
        }

        public void setDd_id(String dd_id) {
            this.dd_id = dd_id;
        }

        public String getDemo_request_id() {
            return this.demo_request_id;
        }

        public void setDemo_request_id(String demo_request_id) {
            this.demo_request_id = demo_request_id;
        }

        public String getDistrict() {
            return this.district;
        }

        public void setDistrict(String district) {
            this.district = district;
        }

        public String getDistrict_id() {
            return this.district_id;
        }

        public void setDistrict_id(String district_id) {
            this.district_id = district_id;
        }

        public String getExpected_farmer() {
            return this.expected_farmer;
        }

        public void setExpected_farmer(String expected_farmer) {
            this.expected_farmer = expected_farmer;
        }

        public String getExpected_pda() {
            return this.expected_pda;
        }

        public void setExpected_pda(String expected_pda) {
            this.expected_pda = expected_pda;
        }

        public String getImage() {
            return this.image;
        }

        public void setImage(String image) {
            this.image = image;
        }

        public String is_deleted() {
            return this.is_deleted;
        }

        public String getLanguage_id() {
            return this.language_id;
        }

        public void setLanguage_id(String language_id) {
            this.language_id = language_id;
        }

        public String getOther_crop_1() {
            return this.other_crop_1;
        }

        public void setOther_crop_1(String other_crop_1) {
            this.other_crop_1 = other_crop_1;
        }

        public String getOther_crop_2() {
            return this.other_crop_2;
        }

        public void setOther_crop_2(String other_crop_2) {
            this.other_crop_2 = other_crop_2;
        }

        public String getPlan_atp_id() {
            return this.plan_atp_id;
        }

        public void setPlan_atp_id(String plan_atp_id) {
            this.plan_atp_id = plan_atp_id;
        }

        public String getPlan_date() {
            return this.plan_date;
        }

        public void setPlan_date(String plan_date) {
            this.plan_date = plan_date;
        }

        public List<Product> getProduct() {
            return this.product;
        }

        public void setProduct(List<Product> product) {
            this.product = product;
        }

        public String getProduct_id() {
            return this.product_id;
        }

        public void setProduct_id(String product_id) {
            this.product_id = product_id;
        }

        public String getReason() {
            return this.reason;
        }

        public void setReason(String reason) {
            this.reason = reason;
        }

        public String getRecord_id() {
            return this.record_id;
        }

        public void setRecord_id(String record_id) {
            this.record_id = record_id;
        }

        public String getState() {
            return this.state;
        }

        public void setState(String state) {
            this.state = state;
        }

        public String getState_id() {
            return this.state_id;
        }

        public void setState_id(String state_id) {
            this.state_id = state_id;
        }

        public String getStatus() {
            return this.status;
        }

        public void setStatus(String status) {
            this.status = status;
        }

        public String getTehsil() {
            return this.tehsil;
        }

        public void setTehsil(String tehsil) {
            this.tehsil = tehsil;
        }

        public String getTehsil_id() {
            return this.tehsil_id;
        }

        public void setTehsil_id(String tehsil_id) {
            this.tehsil_id = tehsil_id;
        }

        public String getUpdated_at() {
            return this.updated_at;
        }

        public void setUpdated_at(String updated_at) {
            this.updated_at = updated_at;
        }

        public String getUser_type() {
            return this.user_type;
        }

        public void setUser_type(String user_type) {
            this.user_type = user_type;
        }

        public String getVillage() {
            return this.village;
        }

        public void setVillage(String village) {
            this.village = village;
        }

        public String getVillage_id() {
            return this.village_id;
        }

        public void setVillage_id(String village_id) {
            this.village_id = village_id;
        }

        public String getTicket_id() {
            return this.ticket_id;
        }

        public void setTicket_id(String ticket_id) {
            this.ticket_id = ticket_id;
        }

        public String getTicket_no() {
            return this.ticket_no;
        }

        public void setTicket_no(String ticket_no) {
            this.ticket_no = ticket_no;
        }

        public String getTotal_farmers() {
            return this.total_farmers;
        }

        public void setTotal_farmers(String total_farmers) {
            this.total_farmers = total_farmers;
        }

        public String getTotal_invited_farmers() {
            return this.total_invited_farmers;
        }

        public void setTotal_invited_farmers(String total_invited_farmers) {
            this.total_invited_farmers = total_invited_farmers;
        }

        public String getDd_name() {
            return this.dd_name;
        }

        public void setDd_name(String dd_name) {
            this.dd_name = dd_name;
        }

        public String getForwhom() {
            return this.forwhom;
        }

        public void setForwhom(String forwhom) {
            this.forwhom = forwhom;
        }

        public Object getLatitude() {
            return this.latitude;
        }

        public void setLatitude(Object latitude) {
            this.latitude = latitude;
        }

        public Object getLongitude() {
            return this.longitude;
        }

        public void setLongitude(Object longitude) {
            this.longitude = longitude;
        }

        public void setIs_deleted(String is_deleted) {
            this.is_deleted = is_deleted;
        }

        @Entity
        public static class Crop implements Serializable {

            @PrimaryKey(autoGenerate = true)
            public int _id;
            public String crop;
            public String crop_id;
            private int aptId;

            public int getAptId() {
                return aptId;
            }

            public void setAptId(int aptId) {
                this.aptId = aptId;
            }

            public String getCrop() {
                return this.crop;
            }


            public String getCrop_id() {
                return this.crop_id;
            }
        }

        @Entity
        public static class Product implements Serializable {

            @PrimaryKey(autoGenerate = true)
            public int _id;
            public String product;
            public String product_id;
            private int aptId;

            public int getAptId() {
                return aptId;
            }

            public void setAptId(int aptId) {
                this.aptId = aptId;
            }

            public String getProduct() {
                return this.product;
            }


            public String getProduct_id() {
                return this.product_id;
            }
        }
    }
}
