package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.Query;

import com.dhanuka.indhan.ATPMenu.activity.ATPBean;

import java.util.List;

@Dao
public interface ATPListDao {

    @Insert
    long insert(ATPBean.Data data);

    @Query("SELECT * FROM Data")
    List<ATPBean.Data> getAllATPList();


    @Query("DELETE FROM Data")
    void deleteTableData();


    @Query("UPDATE Data SET status =:value WHERE plan_atp_id =:atpId")
    void updateStatus(String atpId, String value);


    @Query("UPDATE Data SET reason =:value WHERE plan_atp_id =:atpId")
    void updateReason(String atpId, String value);

    @Query("UPDATE Data SET isTicketClosed =:value WHERE plan_atp_id =:atpId")
    void updateTicketClosed(String atpId, boolean value);


    @Query("UPDATE Data SET isTicketActivated =:value WHERE plan_atp_id =:atpId")
    void updateTicketActivated(String atpId, boolean value);

    @Query("SELECT * FROM Data WHERE isTicketClosed =:value")
    List<ATPBean.Data> getClosedTicketList(boolean value);


    @Query("SELECT * FROM Data WHERE isTicketActivated =:value")
    List<ATPBean.Data> getActivatedList(boolean value);


    @Query("UPDATE Data SET total_invited_farmers =:value WHERE plan_atp_id =:atpId")
    void updateFarmerCount(String atpId, String value);
}