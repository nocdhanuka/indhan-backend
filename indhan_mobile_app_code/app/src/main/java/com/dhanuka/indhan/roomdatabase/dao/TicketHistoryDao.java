package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Delete;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;

import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel;

import java.util.List;

@Dao
public interface TicketHistoryDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    long insert(TicketHistoryModel.Datum data);

    @Query("SELECT * FROM Datum")
    List<TicketHistoryModel.Datum> getAllTicketHistory();

    @Query("SELECT * FROM Datum WHERE isOffline=:isOffline")
    List<TicketHistoryModel.Datum> getAllTicketHistory(boolean isOffline);

    @Query("DELETE FROM Datum")
    void deleteTable();

    @Query("UPDATE Datum SET isTicketClosed =:value WHERE id =:rowId")
    void closeTicket(int rowId, boolean value);


    // Update TicketId as RowId when ticket is created offline.
    @Query("UPDATE Datum SET tID =:value WHERE id =:rowId")
    void updateTicketId(int rowId, int value);

    @Query("UPDATE Datum SET ticketStatus =:value WHERE id =:rowId")
    void updateTicketStatus(int rowId, String value);


    @Query("SELECT * FROM Datum WHERE isOffline=:isOffline")
    List<TicketHistoryModel.Datum> getOnlineUnplannedTicket(boolean isOffline);


    @Query("SELECT * FROM Datum WHERE isTicketATP =:isTicketATP")
    List<TicketHistoryModel.Datum> getPlannedTicket(boolean isTicketATP);

    @Query("SELECT * FROM Datum WHERE isTicketUpdated = :isTiketUpdate")
    List<TicketHistoryModel.Datum> getUpdatedTicket(boolean isTiketUpdate);

    @Query("SELECT * FROM Datum WHERE ticket = :ticketNumber")
    TicketHistoryModel.Datum getOfflineTicket(String ticketNumber);

    @Delete
    void deleteOfflineData(TicketHistoryModel.Datum datum);

    @Query("SELECT * FROM Datum WHERE id = :id")
    TicketHistoryModel.Datum getSingleTicketData(int id);

    @Query("UPDATE Datum SET isTicketATP =:value WHERE ticket =:ticketNumber")
    void updateIsTicketATP(String ticketNumber, boolean value);

    @Query("UPDATE Datum SET totalFarmers =:value WHERE ticket =:ticketNumber")
    void updateTotalFarmer(String ticketNumber, int value);

    @Query("UPDATE Datum SET dataSourceId =:value WHERE id =:rowId")
    void updateDataSourceId(int rowId, String value);

    @Query("UPDATE Datum SET stateId =:value WHERE id =:rowId")
    void updateStateId(int rowId, String value);

    @Query("UPDATE Datum SET districtId =:value WHERE id =:rowId")
    void updateDistrictId(int rowId, String value);

    @Query("UPDATE Datum SET tehsilId =:value WHERE id =:rowId")
    void updateTehsilId(int rowId, String value);

    @Query("UPDATE Datum SET crop1Id =:value WHERE id =:rowId")
    void updateCrop1Id(int rowId, String value);

    @Query("UPDATE Datum SET crop2Id =:value WHERE id =:rowId")
    void updateCrop2Id(int rowId, String value);

    @Query("UPDATE Datum SET crop2 =:value WHERE id =:rowId")
    void updateCrop2Name(int rowId, String value);

    @Query("UPDATE Datum SET crop3Id =:value WHERE id =:rowId")
    void updateCrop3Id(int rowId, String value);

    @Query("UPDATE Datum SET crop3 =:value WHERE id =:rowId")
    void updateCrop3Name(int rowId, String value);

    @Query("UPDATE Datum SET product1Id =:value WHERE id =:rowId")
    void updateProduct1Id(int rowId, String value);

    @Query("UPDATE Datum SET product2Id =:value WHERE id =:rowId")
    void updateProduct2Id(int rowId, String value);

    @Query("UPDATE Datum SET product2 =:value WHERE id =:rowId")
    void updateProduct2Name(int rowId, String value);

    @Query("UPDATE Datum SET product3Id =:value WHERE id =:rowId")
    void updateProduct3Id(int rowId, String value);

    @Query("UPDATE Datum SET product3 =:value WHERE id =:rowId")
    void updateProduct3Name(int rowId, String value);

    @Query("UPDATE Datum SET village =:value WHERE id =:rowId")
    void updateVillageName(int rowId, String value);

    @Query("UPDATE Datum SET village_id =:value WHERE id =:rowId")
    void updateVillageId(int rowId, String value);

    @Query("UPDATE Datum SET language_id =:value WHERE id =:rowId")
    void updateLanguageId(int rowId, String value);

    @Query("UPDATE Datum SET expectedFarmers =:value WHERE id =:rowId")
    void updateExpectedFarmer(int rowId, String value);

    @Query("UPDATE Datum SET expectedPda =:value WHERE id =:rowId")
    void updateExpectedPda(int rowId, String value);

    @Query("UPDATE Datum SET spName1 =:value WHERE id =:rowId")
    void updateName1(int rowId, String value);

    @Query("UPDATE Datum SET spName2 =:value WHERE id =:rowId")
    void updateName2(int rowId, String value);

    @Query("UPDATE Datum SET spName3 =:value WHERE id =:rowId")
    void updateName3(int rowId, String value);

    @Query("UPDATE Datum SET spName4 =:value WHERE id =:rowId")
    void updateName4(int rowId, String value);

    @Query("UPDATE Datum SET spName5 =:value WHERE id =:rowId")
    void updateName5(int rowId, String value);

    @Query("UPDATE Datum SET spName6 =:value WHERE id =:rowId")
    void updateName6(int rowId, String value);

    @Query("UPDATE Datum SET spNumber1 =:value WHERE id =:rowId")
    void updateNumber1(int rowId, String value);

    @Query("UPDATE Datum SET spNumber2 =:value WHERE id =:rowId")
    void updateNumber2(int rowId, String value);

    @Query("UPDATE Datum SET spNumber3 =:value WHERE id =:rowId")
    void updateNumber3(int rowId, String value);

    @Query("UPDATE Datum SET spNumber4 =:value WHERE id =:rowId")
    void updateNumber4(int rowId, String value);

    @Query("UPDATE Datum SET spNumber5 =:value WHERE id =:rowId")
    void updateNumber5(int rowId, String value);

    @Query("UPDATE Datum SET spNumber6 =:value WHERE id =:rowId")
    void updateNumber6(int rowId, String value);

    @Query("UPDATE Datum SET demo_request_id =:value WHERE id =:rowId")
    void updateDemoRequestId(int rowId, String value);

    @Query("UPDATE Datum SET user_farmer =:value WHERE id =:rowId")
    void updateUserFarmer(int rowId, String value);


    @Query("UPDATE Datum SET isTicketUpdated =:value WHERE id =:rowId")
    void isTicketUpdated(int rowId, boolean value);

    @Query("UPDATE Datum SET isFarmerRegistered =:value WHERE ticket =:ticketNumber")
    void isFarmerRegistered(String ticketNumber, boolean value);


    @Query("UPDATE Datum SET isImageUploaded =:value WHERE id =:id")
    void isImageUploaded(int id, boolean value);

    @Query("UPDATE Datum SET isImageUploaded =:value WHERE ticket =:ticketNumber")
    void imageUploaded(String ticketNumber, boolean value);
}