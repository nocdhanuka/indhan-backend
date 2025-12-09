package com.dhanuka.indhan.Activity.RegisterFarmersMVP;

import android.app.Activity;
import android.content.DialogInterface;
import androidx.recyclerview.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.dhanuka.indhan.DataBase.Contact;
import com.dhanuka.indhan.DataBase.DatabaseHandler;
import com.dhanuka.indhan.Fonts.Medium;
import com.dhanuka.indhan.Fonts.Regular;
import com.dhanuka.indhan.R;

import java.text.SimpleDateFormat;
import java.util.List;

public class ManuallyNumberAdapter extends RecyclerView.Adapter<ManuallyNumberAdapter.MyViewHolder> {

    SimpleDateFormat simpleDateFormat;
    String time;
    private ManuallyNumberClickListener clickListener;
    private List<Contact> filteredData = null;
    private Activity mActivityContext;
    DatabaseHandler db = new DatabaseHandler(mActivityContext);

    public ManuallyNumberAdapter(List<Contact> artistCategoryRecordResponseModelArrayList1, Activity mActivityContext) {
        this.filteredData = artistCategoryRecordResponseModelArrayList1;
        this.mActivityContext = mActivityContext;
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
    public void onBindViewHolder(MyViewHolder holder, final int position) {

        Contact artistCategoryRecordResponseModel = filteredData.get(position);
        holder.tvRePhoneNumber.setText(artistCategoryRecordResponseModel.getPhoneNumber());
        /*Date date1 = new Date();
        simpleDateFormat = new SimpleDateFormat("hh:mm a");
        try {
            date1 = simpleDateFormat.parse(artistCategoryRecordResponseModel.getContactName());
        } catch (ParseException e) {
            e.printStackTrace();
        }
        time = simpleDateFormat.format(date1);*/
        String str = artistCategoryRecordResponseModel.getName();
        String[] splited = str.split("\\s+");
        holder.tvMeCallTime.setText(splited[1]);
    }

    @Override
    public int getItemCount() {
        return filteredData == null ? 0 : filteredData.size();
    }

    private void deleteAlert(final int adapterPosition) {
        new androidx.appcompat.app.AlertDialog.Builder(mActivityContext)
                .setTitle(mActivityContext.getResources().getString(R.string.nav_item_delete))
                .setMessage(mActivityContext.getResources().getString(R.string.txt_delete_comment))
                .setPositiveButton("Yes", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {
                        int a = filteredData.get(adapterPosition).getAutoid();
//                        db.deleteSingleContact(a);
                        db.deleteSingleContact(a);
//                        Toast.makeText(mActivityContext, "" + adapterPosition, Toast.LENGTH_SHORT).show();
                        filteredData.remove(adapterPosition);
                        notifyDataSetChanged();
                    }
                })
                .setNegativeButton("No", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {
                    }
                })
                .show();
    }

    public class MyViewHolder extends RecyclerView.ViewHolder {
        private Medium tvMeCallTime;
        private Medium tvMeDelete;
        private Regular tvRePhoneNumber;

        public MyViewHolder(View view) {
            super(view);
            tvMeCallTime = (Medium) view.findViewById(R.id.tvMeCallTime);
            tvMeDelete = (Medium) view.findViewById(R.id.tvMeDelete);
            tvRePhoneNumber = (Regular) view.findViewById(R.id.tvRePhoneNumber);
            tvMeDelete.setVisibility(View.VISIBLE);
            db = new DatabaseHandler(mActivityContext);

            view.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    /*clickListener.onClick(view, filteredData.get(getAdapterPosition()));
                    deleteAlert(getAdapterPosition());*/
                }
            });
            tvMeDelete.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    clickListener.onClick(v, filteredData.get(getAdapterPosition()));
                    deleteAlert(getAdapterPosition());
                }
            });
        }
    }

}