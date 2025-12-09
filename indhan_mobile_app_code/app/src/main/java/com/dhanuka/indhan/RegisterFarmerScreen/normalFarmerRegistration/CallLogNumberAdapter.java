package com.dhanuka.indhan.RegisterFarmerScreen.normalFarmerRegistration;

import android.app.Activity;
import android.content.DialogInterface;
import android.os.AsyncTask;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import androidx.recyclerview.widget.RecyclerView;

import com.dhanuka.indhan.Activity.RegisterFarmersMVP.ManuallyNumberClickListener;
import com.dhanuka.indhan.DataBase.DatabaseHandler;
import com.dhanuka.indhan.Fonts.Medium;
import com.dhanuka.indhan.Fonts.Regular;
import com.dhanuka.indhan.Model.RegisterFarmerContactModel;
import com.dhanuka.indhan.R;
import com.dhanuka.indhan.roomdatabase.AppDatabase;
import com.dhanuka.indhan.roomdatabase.DatabaseClient;
import com.dhanuka.indhan.util.Utility;

import java.text.SimpleDateFormat;
import java.util.List;

/**
 * Created by Shivam on 3/8/2018.
 */

public class CallLogNumberAdapter extends RecyclerView.Adapter<CallLogNumberAdapter.MyViewHolder> {
    SimpleDateFormat simpleDateFormat;
    ListUpdateListener listener;
    private ManuallyNumberClickListener clickListener;
    private List<RegisterFarmerContactModel> filteredData = null;
    private Activity mActivityContext;
    DatabaseHandler db = new DatabaseHandler(mActivityContext);


    public CallLogNumberAdapter(List<RegisterFarmerContactModel> artistCategoryRecordResponseModelArrayList1, Activity mActivityContext) {
        this.filteredData = artistCategoryRecordResponseModelArrayList1;
        this.mActivityContext = mActivityContext;
        listener = (ListUpdateListener) mActivityContext;
    }

    public void setClickListener(ManuallyNumberClickListener itemClickListener) {
        this.clickListener = itemClickListener;
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.number_time_row, parent, false);
        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, int position) {

        RegisterFarmerContactModel currmodel = filteredData.get(position);
        System.out.println("CallLogNumberAdapter.onBindViewHolder : " + currmodel.getContactName());
        if (currmodel.getType().equals("a")) {
            if (currmodel.getContactName() == null || currmodel.getContactName().isEmpty()) {
                holder.tvRePhoneNumber.setText(currmodel.getPhoneNumber().replaceFirst(currmodel.getPhoneNumber().substring(0, 4), "****"));
                holder.tvCallNum.setVisibility(View.GONE);
            } else {
                holder.tvRePhoneNumber.setText(currmodel.getPhoneNumber().replaceFirst(currmodel.getPhoneNumber().substring(0, 4), "****"));
                holder.tvCallNum.setText(currmodel.getPhoneNumber());
                holder.tvCallNum.setVisibility(View.VISIBLE);
            }
        } else {
            holder.tvRePhoneNumber.setText(currmodel.getPhoneNumber());
        }
        simpleDateFormat = new SimpleDateFormat("hh:mm a");
        String date = Utility.getTimefromFormat(currmodel.getTimestamp());
        if (date != null) {
            holder.tvMeCallTime.setText(date);
        }

        if (currmodel.getType().equals("m")) {
            holder.tvMeDelete.setTextColor(mActivityContext.getResources().getColor(R.color.colorButton));
            holder.tvRePhoneNumber.setTextColor(mActivityContext.getResources().getColor(R.color.black));
            holder.tvMeDelete.setText("Delete");
            holder.tvMeDelete.setTag("Delete");
        } else if (currmodel.getType().equals("a")) {
            if (currmodel.getIsDeleted() == 1) { //delete check_sel
                holder.tvMeDelete.setText("Revoke");
                holder.tvMeDelete.setTag("Revoke");
                holder.tvMeDelete.setTextColor(mActivityContext.getResources().getColor(R.color.red));
                holder.tvRePhoneNumber.setTextColor(mActivityContext.getResources().getColor(R.color.red));
            } else {
                holder.tvMeDelete.setTextColor(mActivityContext.getResources().getColor(R.color.colorButton));
                holder.tvRePhoneNumber.setTextColor(mActivityContext.getResources().getColor(R.color.black));
                holder.tvMeDelete.setText("Delete");
                holder.tvMeDelete.setTag("Delete");
            }
        }
        holder.tvMeDelete.setOnClickListener(view -> {
            if (((String) view.getTag()).equalsIgnoreCase("Revoke")) {
                revokeAlert(holder.getAdapterPosition());
            } else {
                deleteAlert(holder.getAdapterPosition(), currmodel.getTicketNumber(), currmodel.getPhoneNumber());
            }
        });
    }

    @Override
    public int getItemCount() {
        return filteredData == null ? 0 : filteredData.size();
    }

    private void deleteAlert(final int adapterPosition, String ticketNumber, String mobileNumber) {
        new androidx.appcompat.app.AlertDialog.Builder(mActivityContext)
                .setTitle(mActivityContext.getResources().getString(R.string.nav_item_delete))
                .setMessage(mActivityContext.getResources().getString(R.string.txt_delete_comment))
                .setPositiveButton("Yes", (dialog, which) -> {
                    if(adapterPosition < filteredData.size()) {
                        filteredData.get(adapterPosition).setIsDeleted(1);
                        filteredData.get(adapterPosition).setIsSync(0);
                        if (filteredData.get(adapterPosition).getType().equalsIgnoreCase("m")) {
                            db.updateDeleteStatusOfContact(filteredData.get(adapterPosition));
                            listener.onUpdateListFromAdapterAction("m", adapterPosition);
                            new UpdateContactStatusInDb(ticketNumber, mobileNumber).execute();
                        } else {
                            db.updateDeleteStatusOfContact(filteredData.get(adapterPosition));
                            listener.onUpdateListFromAdapterAction("a", adapterPosition);
                        }
                    }
                })
                .setNegativeButton("No", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {
                    }
                })
                .show();
    }

    private void revokeAlert(final int adapterPosition) {
        new androidx.appcompat.app.AlertDialog.Builder(mActivityContext)
                .setTitle(mActivityContext.getResources().getString(R.string.nav_item_revoke))
                .setMessage(mActivityContext.getResources().getString(R.string.txt_revoke_comment))
                .setPositiveButton("Yes", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {
                        filteredData.get(adapterPosition).setIsDeleted(0);
                        filteredData.get(adapterPosition).setIsSync(0);
                        db.updateDeleteStatusOfContact(filteredData.get(adapterPosition));
                        listener.onUpdateListFromAdapterAction("a", adapterPosition);
                    }
                })
                .setNegativeButton("No", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {
                    }
                })
                .show();
    }


    interface ListUpdateListener {
        void onUpdateListFromAdapterAction(String type, int position);
    }

    private class UpdateContactStatusInDb extends AsyncTask<Void, Void, Void> {
        String ticketNumber, mobileNumber;

        UpdateContactStatusInDb(String ticketNumber, String mobileNumber) {
            this.ticketNumber = ticketNumber;
            this.mobileNumber = mobileNumber;
        }

        @Override
        protected Void doInBackground(Void... voids) {
            AppDatabase appDatabase = DatabaseClient.getInstance(mActivityContext).getAppDatabase();
            appDatabase.farmerRegistrationDao().contactNumberDelete(ticketNumber, mobileNumber, 1);
            return null;
        }
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {
        private Medium tvMeCallTime, tvCallNum;
        private Medium tvMeDelete;
        private Regular tvRePhoneNumber;

        public MyViewHolder(View view) {
            super(view);
            tvMeCallTime = view.findViewById(R.id.tvMeCallTime);
            tvMeDelete = view.findViewById(R.id.tvMeDelete);
            tvRePhoneNumber = view.findViewById(R.id.tvRePhoneNumber);
            tvCallNum = view.findViewById(R.id.tvCallNum);
            db = new DatabaseHandler(mActivityContext);
        }
    }
}
