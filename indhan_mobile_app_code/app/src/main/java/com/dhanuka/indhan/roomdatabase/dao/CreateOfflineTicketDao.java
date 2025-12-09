package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.Query;

import com.dhanuka.indhan.roomdatabase.model.OfflineTicketModel;

import java.util.List;

@Dao
public interface CreateOfflineTicketDao {
    @Insert
    long insert(OfflineTicketModel offlineTicketModel);

    @Query("SELECT * FROM OfflineTicketModel")
    List<OfflineTicketModel> getOfflineTicketList();

    @Query("DELETE FROM OfflineTicketModel WHERE _id =:id")
    void deleteItem(int id);
}